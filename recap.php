<?php
session_start();
require_once __DIR__ . '/db.php';

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
                                updated_at = CURRENT_TIMESTAMP
                            WHERE email = :original_email";

                    $stmt = $pdo->prepare($sql);
                }

                $languesJson = json_encode($_SESSION['langues'] ?? []);
                $centresJson = json_encode($_SESSION['centres'] ?? []);
                $projectsJson = json_encode($_SESSION['projects'] ?? []);
                $modulesJson = json_encode($_SESSION['modules'] ?? []);

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
                                langues, centres, projects, modules, remarques, document_path
                            ) VALUES (
                                :email, :nom, :prenom, :age, :tel_num, :annee, :filiere,
                                :langues, :centres, :projects, :modules, :remarques, :document_path
                            )";

                    $stmt = $pdo->prepare($sql);

                    $languesJson = json_encode($_SESSION['langues'] ?? []);
                    $centresJson = json_encode($_SESSION['centres'] ?? []);
                    $projectsJson = json_encode($_SESSION['projects'] ?? []);
                    $modulesJson = json_encode($_SESSION['modules'] ?? []);

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif</title>
</head>
<body>
    <h1>Fiche de Renseignements - Récapitulatif</h1>
    <hr>
    <?php if (!empty($_SESSION['error'])): ?>
        <div style="color: #b00020; font-weight: bold;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
        <hr>
    <?php endif; ?>

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

    <div>
        <form action="button_valider.php" method="POST" style="display: inline;">
            <button type="submit">Valider (.txt)</button>
        </form>
        <form action="button_modifier.php" method="POST" style="display: inline;">
            <button type="submit">Modifier</button>
        </form>
    </div>
</body>
</html>