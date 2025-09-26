<?php
session_start();
require_once __DIR__ . '/fiche_db.php';

// Stockage des données en session
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION['nom'] = $_POST['nom'];
    $_SESSION['prenom'] = $_POST['prenom'];
    $_SESSION['age'] = $_POST['age'];
    $_SESSION['tel_num'] = $_POST['tel_num'];
    // Normaliser l'email (trim + minuscules) pour valider et stocker
    $_POST['email'] = strtolower(trim($_POST['email']));
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['filiere'] = isset($_POST['filiere']) ? $_POST['filiere'] : '';
    $_SESSION['annee'] = isset($_POST['annee']) ? $_POST['annee'] : '';
    $_SESSION['langues'] = isset($_POST['langues']) ? $_POST['langues'] : [];
    $_SESSION['centres'] = isset($_POST['centres']) ? $_POST['centres'] : [];
    $_SESSION['projects'] = isset($_POST['projects']) ? $_POST['projects'] : [];
    $_SESSION['modules'] = isset($_POST['modules']) ? $_POST['modules'] : [];
    $_SESSION['remarques'] = isset($_POST['remarques']) ? $_POST['remarques'] : '';
    // Champs CV supplémentaires depuis le formulaire unifié (s'ils existent)
    $_SESSION['adresse'] = isset($_POST['adresse']) ? $_POST['adresse'] : ($_SESSION['adresse'] ?? null);
    $_SESSION['ville'] = isset($_POST['ville']) ? $_POST['ville'] : ($_SESSION['ville'] ?? null);
    $_SESSION['linkedin'] = isset($_POST['linkedin']) ? $_POST['linkedin'] : ($_SESSION['linkedin'] ?? null);
    $_SESSION['formations'] = isset($_POST['formations']) ? $_POST['formations'] : ($_SESSION['formations'] ?? ['titre'=>[], 'description'=>[]]);
    $_SESSION['stages'] = isset($_POST['stages']) ? $_POST['stages'] : ($_SESSION['stages'] ?? ['poste'=>[], 'entreprise'=>[]]);
    $_SESSION['competences'] = isset($_POST['competences']) ? $_POST['competences'] : ($_SESSION['competences'] ?? []);
    $_SESSION['langues_cv'] = isset($_POST['langues_cv']) ? $_POST['langues_cv'] : ($_SESSION['langues_cv'] ?? ['nom'=>[], 'niveau'=>[]]);
    // Champs candidature de stage
    $_SESSION['internship_type'] = isset($_POST['internship_type']) ? $_POST['internship_type'] : ($_SESSION['internship_type'] ?? null);
    $_SESSION['internship_duration'] = isset($_POST['internship_duration']) ? $_POST['internship_duration'] : ($_SESSION['internship_duration'] ?? null);
    $_SESSION['cover_letter'] = isset($_POST['cover_letter']) ? $_POST['cover_letter'] : ($_SESSION['cover_letter'] ?? null);
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // créer le dossier s'il n'existe pas
    }

    $fileTmpPath = $_FILES['document']['tmp_name'];
    $fileName = basename($_FILES['document']['name']);
    $destPath = $uploadDir . $fileName;

    // Déplacer le fichier
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $_SESSION['document'] = $destPath;
    } else {
        $_SESSION['document'] = "Erreur lors du téléchargement";
    }
    }
    // Upload de la photo (CV) si fournie
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDirPhoto = 'uploads_photo/';
        if (!is_dir($uploadDirPhoto)) {
            mkdir($uploadDirPhoto, 0777, true);
        }
        $tmp = $_FILES['photo']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $dest = $uploadDirPhoto . $fileName;
        if (move_uploaded_file($tmp, $dest)) {
            $_SESSION['photo'] = $dest;
        }
    }
    // Validation email côté PHP
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Adresse email invalide.';
    } else {
        // Insertion ou modification selon le mode
        try {
            ensure_schema();
            $pdo = get_pdo();

            $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
            $originalEmail = isset($_POST['original_email']) ? strtolower(trim($_POST['original_email'])) : '';

            if ($mode === 'modifier' && !empty($originalEmail)) {
                // Cas modification
                if ($originalEmail === $_POST['email']) {
                    // Email inchangé → mise à jour directe
                    $sql = "UPDATE users SET
                                nom = :nom,
                                prenom = :prenom,
                                age = :age,
                                tel_num = :tel_num,
                                annee = :annee,
                                filiere = :filiere,
                                langues = :langues,
                                centres = :centres,
                                projects = :projects,
                                modules = :modules,
                                remarques = :remarques,
                                document_path = :document_path,
                                adresse = :adresse,
                                ville = :ville,
                                linkedin = :linkedin,
                                photo_path = :photo_path,
                                formations = :formations,
                                stages = :stages,
                                competences = :competences,
                                langues_cv = :langues_cv,
                                internship_type = :internship_type,
                                internship_duration = :internship_duration,
                                cover_letter = :cover_letter,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE email = :email";

                    $stmt = $pdo->prepare($sql);
                } else {
                    // Email modifié → vérifier collision
                    $check = $pdo->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
                    $check->execute([':email' => $_POST['email']]);
                    if ($check->fetchColumn()) {
                        $_SESSION['error'] = "Cet email existe déjà. Impossible de modifier.";
                        throw new Exception('Email already exists');
                    }

                    // Mettre à jour la clé primaire (changer l'email)
                    $sql = "UPDATE users SET
                                email = :email,
                                nom = :nom,
                                prenom = :prenom,
                                age = :age,
                                tel_num = :tel_num,
                                annee = :annee,
                                filiere = :filiere,
                                langues = :langues,
                                centres = :centres,
                                projects = :projects,
                                modules = :modules,
                                remarques = :remarques,
                                document_path = :document_path,
                                adresse = :adresse,
                                ville = :ville,
                                linkedin = :linkedin,
                                photo_path = :photo_path,
                                formations = :formations,
                                stages = :stages,
                                competences = :competences,
                                langues_cv = :langues_cv,
                                internship_type = :internship_type,
                                internship_duration = :internship_duration,
                                cover_letter = :cover_letter,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE email = :original_email";

                    $stmt = $pdo->prepare($sql);
                }

                $languesJson = json_encode($_SESSION['langues'] ?? []);
                $centresJson = json_encode($_SESSION['centres'] ?? []);
                $projectsJson = json_encode($_SESSION['projects'] ?? []);
                $modulesJson = json_encode($_SESSION['modules'] ?? []);
                $formationsJson = json_encode($_SESSION['formations'] ?? ['titre'=>[], 'description'=>[]], JSON_UNESCAPED_UNICODE);
                $stagesJson = json_encode($_SESSION['stages'] ?? ['poste'=>[], 'entreprise'=>[]], JSON_UNESCAPED_UNICODE);
                $competencesJson = json_encode($_SESSION['competences'] ?? [], JSON_UNESCAPED_UNICODE);
                $languesCvJson = json_encode($_SESSION['langues_cv'] ?? ['nom'=>[], 'niveau'=>[]], JSON_UNESCAPED_UNICODE);

                $params = [
                    ':email' => $_POST['email'],
                    ':nom' => $_POST['nom'],
                    ':prenom' => $_POST['prenom'],
                    ':age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
                    ':tel_num' => $_POST['tel_num'] ?? null,
                    ':annee' => $_POST['annee'] ?? null,
                    ':filiere' => $_POST['filiere'] ?? null,
                    ':langues' => $languesJson,
                    ':centres' => $centresJson,
                    ':projects' => $projectsJson,
                    ':modules' => $modulesJson,
                    ':remarques' => $_SESSION['remarques'] ?? null,
                    ':document_path' => $_SESSION['document'] ?? null,
                    ':adresse' => $_SESSION['adresse'] ?? null,
                    ':ville' => $_SESSION['ville'] ?? null,
                    ':linkedin' => $_SESSION['linkedin'] ?? null,
                    ':photo_path' => $_SESSION['photo'] ?? null,
                    ':formations' => $formationsJson,
                    ':stages' => $stagesJson,
                    ':competences' => $competencesJson,
                    ':langues_cv' => $languesCvJson,
                    ':internship_type' => $_SESSION['internship_type'] ?? null,
                    ':internship_duration' => $_SESSION['internship_duration'] ?? null,
                    ':cover_letter' => $_SESSION['cover_letter'] ?? null,
                ];
                if ($originalEmail !== $_POST['email']) {
                    $params[':original_email'] = $originalEmail;
                }
                $stmt->execute($params);
            } else {
                // Cas insertion (nouvel enregistrement) → bloquer si email déjà existant
                $check = $pdo->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
                $check->execute([':email' => $_POST['email']]);
                if ($check->fetchColumn()) {
                    $_SESSION['error'] = "Cet email existe déjà. L'envoi est bloqué.";
                } else {
                    $sql = "INSERT INTO users (
                                email, nom, prenom, age, tel_num, annee, filiere,
                                langues, centres, projects, modules, remarques, document_path,
                                adresse, ville, linkedin, photo_path, formations, stages, competences, langues_cv,
                                internship_type, internship_duration, cover_letter
                            ) VALUES (
                                :email, :nom, :prenom, :age, :tel_num, :annee, :filiere,
                                :langues, :centres, :projects, :modules, :remarques, :document_path,
                                :adresse, :ville, :linkedin, :photo_path, :formations, :stages, :competences, :langues_cv,
                                :internship_type, :internship_duration, :cover_letter
                            )";

                    $stmt = $pdo->prepare($sql);

                    $languesJson = json_encode($_SESSION['langues'] ?? []);
                    $centresJson = json_encode($_SESSION['centres'] ?? []);
                    $projectsJson = json_encode($_SESSION['projects'] ?? []);
                    $modulesJson = json_encode($_SESSION['modules'] ?? []);
                    $formationsJson = json_encode($_SESSION['formations'] ?? ['titre'=>[], 'description'=>[]], JSON_UNESCAPED_UNICODE);
                    $stagesJson = json_encode($_SESSION['stages'] ?? ['poste'=>[], 'entreprise'=>[]], JSON_UNESCAPED_UNICODE);
                    $competencesJson = json_encode($_SESSION['competences'] ?? [], JSON_UNESCAPED_UNICODE);
                    $languesCvJson = json_encode($_SESSION['langues_cv'] ?? ['nom'=>[], 'niveau'=>[]], JSON_UNESCAPED_UNICODE);

                    $stmt->execute([
                        ':email' => $_POST['email'],
                        ':nom' => $_POST['nom'],
                        ':prenom' => $_POST['prenom'],
                        ':age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
                        ':tel_num' => $_POST['tel_num'] ?? null,
                        ':annee' => $_POST['annee'] ?? null,
                        ':filiere' => $_POST['filiere'] ?? null,
                        ':langues' => $languesJson,
                        ':centres' => $centresJson,
                        ':projects' => $projectsJson,
                        ':modules' => $modulesJson,
                        ':remarques' => $_SESSION['remarques'] ?? null,
                        ':document_path' => $_SESSION['document'] ?? null,
                        ':adresse' => $_SESSION['adresse'] ?? null,
                        ':ville' => $_SESSION['ville'] ?? null,
                        ':linkedin' => $_SESSION['linkedin'] ?? null,
                        ':photo_path' => $_SESSION['photo'] ?? null,
                        ':formations' => $formationsJson,
                        ':stages' => $stagesJson,
                        ':competences' => $competencesJson,
                        ':langues_cv' => $languesCvJson,
                        ':internship_type' => $_SESSION['internship_type'] ?? null,
                        ':internship_duration' => $_SESSION['internship_duration'] ?? null,
                        ':cover_letter' => $_SESSION['cover_letter'] ?? null,
                    ]);
                }
            }
        } catch (Throwable $e) {
            if (!isset($_SESSION['error'])) {
                $_SESSION['error'] = 'Erreur base de données: ' . $e->getMessage();
            }
        }
    }
}

// Récupération des données
$nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : 'Non défini';
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'Non défini';
$age = isset($_SESSION['age']) ? $_SESSION['age'] : 'Non défini';
$tel_num = isset($_SESSION['tel_num']) ? $_SESSION['tel_num'] : 'Non défini';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Non défini';
$annee = isset($_SESSION['annee']) ? $_SESSION['annee'] : 'Non défini';
$filiere = isset($_SESSION['filiere']) ? $_SESSION['filiere'] : 'Non défini';
$langues = isset($_SESSION['langues']) ? $_SESSION['langues'] : ['Non défini'];
$centres = isset($_SESSION['centres']) ? $_SESSION['centres'] : ['Non défini'];
$projects = isset($_SESSION['projects']) ? $_SESSION['projects'] : ['Non défini'];
$modules = isset($_SESSION['modules']) ? $_SESSION['modules'] : ['Non défini'];
$remarque = isset($_SESSION['remarques']) ? $_SESSION['remarques'] : 'Non défini';
$document = isset($_SESSION['document']) ? $_SESSION['document'] : 'Aucun fichier fourni';

// Champs candidature de stage pour affichage
$internship_type = isset($_SESSION['internship_type']) ? $_SESSION['internship_type'] : 'Non défini';
$internship_duration = isset($_SESSION['internship_duration']) ? $_SESSION['internship_duration'] : 'Non défini';
$cover_letter = isset($_SESSION['cover_letter']) ? $_SESSION['cover_letter'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
    <hr>
    <?php if (!empty($_SESSION['error'])): ?>
        <div style="color: #b00020; font-weight: bold;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
        <hr>
    <?php endif; ?>
    <h1>Fiche de Renseignements - Récapitulatif</h1>
    <hr>

    <h2>Candidature de Stage</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><strong>Type de stage</strong></td>
            <td><?= htmlspecialchars($internship_type); ?></td>
        </tr>
        <tr>
            <td><strong>Durée</strong></td>
            <td><?= htmlspecialchars($internship_duration); ?></td>
        </tr>
        <tr>
            <td><strong>Lettre de motivation</strong></td>
            <td><?= nl2br(htmlspecialchars($cover_letter)); ?></td>
        </tr>
    </table>
    

    <h2>Renseignements Personnels</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><strong>Nom</strong></td>
            <td><?= $nom; ?></td>
        </tr>
        <tr>
            <td><strong>Prénom</strong></td>
            <td><?= $prenom; ?></td>
        </tr>
        <tr>
            <td><strong>Âge</strong></td>
            <td><?= $age; ?></td>
        </tr>
        <tr>
            <td><strong>Téléphone</strong></td>
            <td><?= $tel_num; ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><?= $email; ?></td>
        </tr>
    </table>
    <hr>

    <h2>Renseignements Académiques</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><strong>Niveau</strong></td>
            <td><?= $annee; ?></td>
        </tr>
        <tr>
            <td><strong>Fillière</strong></td>
            <td><?= $filiere; ?></td>
        </tr>
        <tr>
            <td><strong>Modules</strong></td>
            <td>
                <ul>
                    <?php foreach ($modules as $module): ?>
                        <li><?= $module; ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>
    <hr>

    <?php if (!empty($remarque) && $remarque !== 'Non défini'): ?>
    <h2>Remarques</h2>
    <p><?= htmlspecialchars($remarque); ?></p>
    <hr>
    <?php endif; ?>
    
    <h2>Fichier Uploadé</h2>
    <p>
    <?php if ($document !== "Aucun fichier fourni" && $document !== "Erreur lors du téléchargement"): ?>
        <a href="<?= $document; ?>" target="_blank">Voir le fichier</a>
    <?php else: ?>
        <?= $document; ?>
    <?php endif; ?>
    </p>
    <hr>

    <h2>Informations Complémentaires</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><strong>Projets Réalisés</strong></td>
            <td>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li><?= empty($project) ? "Non défini" : $project; ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Centres d'intérêt</strong></td>
            <td>
                <ul>
                    <?php foreach ($centres as $centre): ?>
                        <li><?= empty($centre) ? "Non défini" : $centre; ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Langues Parlées</strong></td>
            <td>
                <ul>
                    <?php foreach ($langues as $langue): ?>
                        <li><?= empty($langue) ? "Non défini" : $langue; ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>
    <hr>

    <h2>Sections CV</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td><strong>Adresse</strong></td>
            <td><?= htmlspecialchars($_SESSION['adresse'] ?? ''); ?></td>
        </tr>
        <tr>
            <td><strong>Ville</strong></td>
            <td><?= htmlspecialchars($_SESSION['ville'] ?? ''); ?></td>
        </tr>
        <tr>
            <td><strong>LinkedIn</strong></td>
            <td><?= htmlspecialchars($_SESSION['linkedin'] ?? ''); ?></td>
        </tr>
        <tr>
            <td><strong>Photo</strong></td>
            <td>
                <?php if (!empty($_SESSION['photo']) && file_exists($_SESSION['photo'])): ?>
                    <img src="<?= $_SESSION['photo']; ?>" alt="Photo" style="max-height:120px;">
                <?php else: ?>
                    Non fournie
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><strong>Formations</strong></td>
            <td>
                <ul>
                <?php $ft = $_SESSION['formations']['titre'] ?? []; $fd = $_SESSION['formations']['description'] ?? []; $mx = max(count($ft), count($fd)); for ($i=0;$i<$mx;$i++): ?>
                    <li><?= htmlspecialchars(($ft[$i] ?? '') . (isset($fd[$i]) && $fd[$i]!=='' ? ' : '.$fd[$i] : '')); ?></li>
                <?php endfor; ?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Expériences</strong></td>
            <td>
                <ul>
                <?php $sp = $_SESSION['stages']['poste'] ?? []; $se = $_SESSION['stages']['entreprise'] ?? []; $mx = max(count($sp), count($se)); for ($i=0;$i<$mx;$i++): ?>
                    <li><?= htmlspecialchars(($sp[$i] ?? '') . (isset($se[$i]) && $se[$i]!=='' ? ' chez '.$se[$i] : '')); ?></li>
                <?php endfor; ?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Compétences</strong></td>
            <td>
                <ul>
                    <?php foreach (($_SESSION['competences'] ?? []) as $c): ?>
                        <li><?= htmlspecialchars($c); ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Langues (CV)</strong></td>
            <td>
                <ul>
                <?php $ln = $_SESSION['langues_cv']['nom'] ?? []; $lv = $_SESSION['langues_cv']['niveau'] ?? []; $m = max(count($ln), count($lv)); for ($i=0;$i<$m;$i++): ?>
                    <li><?= htmlspecialchars(($ln[$i] ?? '') . (isset($lv[$i]) && $lv[$i]!=='' ? ' ('.$lv[$i].')' : '')); ?></li>
                <?php endfor; ?>
                </ul>
            </td>
        </tr>
    </table>

    <div style="margin-top:16px;">
        <form action="button_valider.php" method="POST" style="display: inline;">
            <button type="submit">Valider (.txt)</button>
        </form>
        <form action="button_modifier.php" method="POST" style="display: inline;">
            <button type="submit">Modifier</button>
        </form>
        <form action="button_CV.php" method="POST" style="display: inline;" onsubmit="return injectSessionIntoForm(this)">
            <button type="submit">Générer PDF CV</button>
        </form>
    </div>

    <script>
        function injectSessionIntoForm(form){
            const data = <?php echo json_encode([
                'nom' => $_SESSION['nom'] ?? '',
                'prenom' => $_SESSION['prenom'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'tel_num' => $_SESSION['tel_num'] ?? '',
                'adresse' => $_SESSION['adresse'] ?? '',
                'ville' => $_SESSION['ville'] ?? '',
                'linkedin' => $_SESSION['linkedin'] ?? '',
                'photo' => $_SESSION['photo'] ?? '',
                'formations' => $_SESSION['formations'] ?? ['titre'=>[], 'description'=>[]],
                'stages' => $_SESSION['stages'] ?? ['poste'=>[], 'entreprise'=>[]],
                'competences' => $_SESSION['competences'] ?? [],
                'langues' => $_SESSION['langues_cv'] ?? ['nom'=>[], 'niveau'=>[]],
                'centres' => $_SESSION['centres'] ?? [],
            ], JSON_UNESCAPED_UNICODE); ?>;
            while(form.firstChild){ form.removeChild(form.firstChild); }
            const add = (n,v) => {
                if (Array.isArray(v)) {
                    v.forEach(val => add(n+'[]', val));
                } else if (v && typeof v === 'object') {
                    Object.keys(v).forEach(k => add(n+'['+k+']', v[k]));
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = n;
                    input.value = v ?? '';
                    form.appendChild(input);
                }
            };
            Object.keys(data).forEach(k => add(k, data[k]));
            return true;
        }
    </script>
<main>
</body>
</html>