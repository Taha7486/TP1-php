<?php
session_start();

// Pré-remplissage depuis la session si mode modifier
$prefill = isset($_GET['value']);

// Fiche (ancienne) champs
$nom = $prefill ? ($_SESSION['nom'] ?? '') : '';
$prenom = $prefill ? ($_SESSION['prenom'] ?? '') : '';
$age = $prefill ? ($_SESSION['age'] ?? '') : '';
$tel_num = $prefill ? ($_SESSION['tel_num'] ?? '') : '';
$email = $prefill ? ($_SESSION['email'] ?? '') : '';
$annee = $prefill ? ($_SESSION['annee'] ?? '') : '';
$filiere = $prefill ? ($_SESSION['filiere'] ?? '') : '';
$langues = $prefill ? ($_SESSION['langues'] ?? []) : [];
$centres = $prefill ? ($_SESSION['centres'] ?? []) : [];
$projects = $prefill ? ($_SESSION['projects'] ?? []) : [];
$modules = $prefill ? ($_SESSION['modules'] ?? []) : [];
$remarques = $prefill ? ($_SESSION['remarques'] ?? '') : '';
$document_path = $prefill ? ($_SESSION['document'] ?? '') : '';

// CV champs
$adresse = $prefill ? ($_SESSION['adresse'] ?? '') : '';
$ville = $prefill ? ($_SESSION['ville'] ?? '') : '';
$linkedin = $prefill ? ($_SESSION['linkedin'] ?? '') : '';
$photo = $prefill ? ($_SESSION['photo'] ?? '') : '';
$formations = $prefill ? ($_SESSION['formations'] ?? ['titre'=>[''],'description'=>['']]) : ['titre'=>[''],'description'=>['']];
$stages = $prefill ? ($_SESSION['stages'] ?? ['poste'=>[''],'entreprise'=>['']]) : ['poste'=>[''],'entreprise'=>['']];
$competences = $prefill ? ($_SESSION['competences'] ?? ['']) : [''];
$langues_cv = $prefill ? ($_SESSION['langues_cv'] ?? ['nom'=>[''],'niveau'=>['Debutant']]) : ['nom'=>[''],'niveau'=>['Debutant']];

// Champs de candidature de stage
$internship_type = $prefill ? ($_SESSION['internship_type'] ?? '') : '';
$internship_duration = $prefill ? ($_SESSION['internship_duration'] ?? '') : '';
$cover_letter = $prefill ? ($_SESSION['cover_letter'] ?? '') : '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Unifié</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Formulaire unique: Fiche + CV</h1>
    <form action="recap.php" method="post" enctype="multipart/form-data">
        <h2>Renseignements Personnels</h2>
        <table>
            <tr>
                <td><label for="nom">Nom :</label></td>
                <td><input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required></td>
            </tr>
            <tr>
                <td><label for="prenom">Prénom :</label></td>
                <td><input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required></td>
            </tr>
            <tr>
                <td><label for="age">Âge :</label></td>
                <td><input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" min="0"></td>
            </tr>
            <tr>
                <td><label for="tel_num">Téléphone :</label></td>
                <td><input type="tel" id="tel_num" name="tel_num" value="<?php echo htmlspecialchars($tel_num); ?>" required></td>
            </tr>
            <tr>
                <td><label for="email">Email :</label></td>
                <td><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td>
            </tr>
            <tr>
                <td><label for="adresse">Adresse :</label></td>
                <td><input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>"></td>
            </tr>
            <tr>
                <td><label for="ville">Ville :</label></td>
                <td><input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($ville); ?>"></td>
            </tr>
            <tr>
                <td><label for="linkedin">LinkedIn :</label></td>
                <td><input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($linkedin); ?>" placeholder="https://www.linkedin.com/in/votreprofil"></td>
            </tr>
            <tr>
                <td><label for="photo">Photo :</label></td>
                <td>
                    <?php if (!empty($photo) && file_exists($photo)): ?>
                        <div>
                            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo" style="max-height:80px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="photo" name="photo" accept="image/*">
                </td>
            </tr>
        </table>

        <hr>
        <h2>Renseignements Académiques</h2>
        <h4>Vous êtes en :</h4>
        <table>
            <tr>
                <?php
                $annees = ['1ère année','2ème année','3ème année','4ème année','5ème année'];
                foreach ($annees as $i => $an) {
                    $checked = ($annee === $an) ? 'checked' : '';
                    echo '<td>';
                    echo '<input type="radio" id="an'.$i.'" name="annee" value="'.htmlspecialchars($an).'" onchange="updateModules()" '.$checked.'>';
                    echo '<label for="an'.$i.'">'.htmlspecialchars($an).'</label>';
                    echo '</td>';
                }
                ?>
            </tr>
        </table>

        <div id="filieres" style="display:none;">
            <h4>Filière :</h4>
            <?php
            $filieres = ['GSTR','GI','SCM','GC','GM','BDIA'];
            foreach ($filieres as $f) {
                $checkedF = ($filiere === $f) ? 'checked' : '';
                echo '<label style="margin-right:12px;">';
                echo '<input type="radio" name="filiere" value="'.htmlspecialchars($f).'" onchange="updateModules()" '.$checkedF.'> '.$f;
                echo '</label>';
            }
            ?>
        </div>

        <h4>Modules suivis cette année :</h4>
        <div id="modules-container"></div>

        <hr>
        <h3>Vos remarques</h3>
        <textarea name="remarques" rows="5" cols="60"><?php echo htmlspecialchars($remarques); ?></textarea>

        <h4>Fichier Uploadé :</h4>
        <?php if (!empty($document_path) && file_exists($document_path)): ?>
            <p>Fichier actuel : <a href="<?php echo htmlspecialchars($document_path); ?>" target="_blank"><?php echo htmlspecialchars(basename($document_path)); ?></a></p>
            <p>Si vous voulez le remplacer, choisissez un nouveau fichier :</p>
        <?php endif; ?>
        <input type="file" name="document">
        <hr>
        <h2>Informations Complémentaires (Fiche)</h2>
        <h4>Projets réalisés :</h4>
        <div id="projects-container">
            <?php if (!empty($projects)) : foreach ($projects as $idx => $pr): ?>
                <div style="margin-bottom:5px;">
                    <input type="text" name="projects[]" value="<?php echo htmlspecialchars($pr); ?>" placeholder="Nom du projet" style="width:300px;">
                    <?php if ($idx > 0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endforeach; else: ?>
                <div style="margin-bottom:5px;"><input type="text" name="projects[]" placeholder="Nom du projet" style="width:300px;"></div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addField('projects-container','projects','Nom du projet')">Ajouter un projet</button>

        <h4>Vos centres d'intérêt :</h4>
        <div id="centres-container">
            <?php if (!empty($centres)) : foreach ($centres as $idx => $ct): ?>
                <div style="margin-bottom:5px;">
                    <input type="text" name="centres[]" value="<?php echo htmlspecialchars($ct); ?>" placeholder="Centre d\'intérêt" style="width:300px;">
                    <?php if ($idx > 0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endforeach; else: ?>
                <div style="margin-bottom:5px;"><input type="text" name="centres[]" placeholder="Centre d\'intérêt" style="width:300px;"></div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addField('centres-container','centres','Centre d\'intérêt')">Ajouter un centre</button>

        <h4>Langues parlées (Fiche)</h4>
        <div id="langues-container">
            <?php if (!empty($langues)) : foreach ($langues as $idx => $lg): ?>
                <div style="margin-bottom:5px;">
                    <input type="text" name="langues[]" value="<?php echo htmlspecialchars($lg); ?>" placeholder="Langue" style="width:300px;">
                    <?php if ($idx > 0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endforeach; else: ?>
                <div style="margin-bottom:5px;"><input type="text" name="langues[]" placeholder="Langue" style="width:300px;"></div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addField('langues-container','langues','Langue')">Ajouter une langue</button>

        <hr>
        <h2>Sections CV</h2>
        <h3>Formations</h3>
        <div id="formations-container">
            <?php $ft = $formations['titre'] ?? ['']; $fd = $formations['description'] ?? ['']; $max = max(count($ft), count($fd)); for ($i=0;$i<$max;$i++): ?>
                <div>
                    <input type="text" name="formations[titre][]" value="<?php echo htmlspecialchars($ft[$i] ?? ''); ?>" placeholder="Diplôme / Formation">
                    <input type="text" name="formations[description][]" value="<?php echo htmlspecialchars($fd[$i] ?? ''); ?>" placeholder="Description">
                    <?php if ($i>0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <button type="button" onclick="addFormation()">Ajouter une formation</button>

        <h3>Stages / Expériences</h3>
        <div id="stages-container">
            <?php $sp = $stages['poste'] ?? ['']; $se = $stages['entreprise'] ?? ['']; $mx = max(count($sp), count($se)); for ($i=0;$i<$mx;$i++): ?>
                <div>
                    <input type="text" name="stages[poste][]" value="<?php echo htmlspecialchars($sp[$i] ?? ''); ?>" placeholder="Intitulé du poste">
                    <input type="text" name="stages[entreprise][]" value="<?php echo htmlspecialchars($se[$i] ?? ''); ?>" placeholder="Entreprise">
                    <?php if ($i>0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <button type="button" onclick="addStage()">Ajouter un stage</button>

        <h3>Compétences</h3>
        <div id="competences-container">
            <?php if (!empty($competences)) : foreach ($competences as $i => $c): ?>
                <div>
                    <input type="text" name="competences[]" value="<?php echo htmlspecialchars($c); ?>" placeholder="Compétence">
                    <?php if ($i>0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endforeach; else: ?>
                <div><input type="text" name="competences[]" placeholder="Compétence"></div>
            <?php endif; ?>
        </div>
        <button type="button" onclick="addCompetence()">Ajouter une compétence</button>

        <h3>Langues (CV)</h3>
        <div id="langues-cv-container">
            <?php $ln = $langues_cv['nom'] ?? ['']; $lv = $langues_cv['niveau'] ?? ['Debutant']; $m = max(count($ln), count($lv)); for ($i=0;$i<$m;$i++): ?>
                <div>
                    <input type="text" name="langues_cv[nom][]" value="<?php echo htmlspecialchars($ln[$i] ?? ''); ?>" placeholder="Langue">
                    <select name="langues_cv[niveau][]">
                        <?php foreach (['Debutant','Intermediaire','Courant'] as $opt): $sel = (($lv[$i] ?? 'Debutant') === $opt) ? 'selected' : ''; ?>
                            <option value="<?php echo $opt; ?>" <?php echo $sel; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($i>0): ?><button type="button" onclick="this.parentElement.remove()">Effacer</button><?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <button type="button" onclick="addLangueCV()">Ajouter une langue</button>
        <hr>
        <h2>Candidature de Stage</h2>
        <table>
            <tr>
                <td><label for="internship_type">Type de stage :</label></td>
                <td>
                    <select id="internship_type" name="internship_type" required>
                        <?php
                        $types = ['Observation','PFA','PFE','Autre'];
                        foreach ($types as $t) {
                            $sel = ($internship_type === $t) ? 'selected' : '';
                            echo '<option value="'.htmlspecialchars($t).'" '.$sel.'>'.htmlspecialchars($t).'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="internship_duration">Durée (semaines ou dates) :</label></td>
                <td><input type="text" id="internship_duration" name="internship_duration" value="<?php echo htmlspecialchars($internship_duration); ?>" placeholder="8 semaines, Juin-Août, ..." required></td>
            </tr>
            <tr>
                <td style="vertical-align: top;"><label for="cover_letter">Lettre de motivation :</label></td>
                <td><textarea id="cover_letter" name="cover_letter" rows="6" cols="60" placeholder="Expliquez vos motivations..." required><?php echo htmlspecialchars($cover_letter); ?></textarea></td>
            </tr>
        </table>
        <hr>
        <input type="submit" name="envoyer" value="Envoyer">
    </form>

    <script>
        function addField(containerId, inputName, placeholder) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.style.marginBottom = '5px';
            div.innerHTML = '<input type="text" name="'+inputName+'[]" placeholder="'+placeholder+'" style="width:300px;">'+
                            ' <button type="button" onclick="this.parentElement.remove()">Effacer</button>';
            container.appendChild(div);
        }
        function addFormation() {
            document.getElementById('formations-container').insertAdjacentHTML('beforeend',
                '<div><input type="text" name="formations[titre][]" placeholder="Diplôme / Formation">'+
                ' <input type="text" name="formations[description][]" placeholder="Description">'+
                ' <button type="button" onclick="this.parentElement.remove()">Effacer</button></div>'
            );
        }
        function addStage() {
            document.getElementById('stages-container').insertAdjacentHTML('beforeend',
                '<div><input type="text" name="stages[poste][]" placeholder="Intitulé du poste">'+
                ' <input type="text" name="stages[entreprise][]" placeholder="Entreprise">'+
                ' <button type="button" onclick="this.parentElement.remove()">Effacer</button></div>'
            );
        }
        function addCompetence() {
            document.getElementById('competences-container').insertAdjacentHTML('beforeend',
                '<div><input type="text" name="competences[]" placeholder="Compétence">'+
                ' <button type="button" onclick="this.parentElement.remove()">Effacer</button></div>'
            );
        }
        function addLangueCV() {
            document.getElementById('langues-cv-container').insertAdjacentHTML('beforeend',
                '<div><input type="text" name="langues_cv[nom][]" placeholder="Langue">'+
                ' <select name="langues_cv[niveau][]"><option value="Debutant">Debutant</option><option value="Intermediaire">Intermediaire</option><option value="Courant">Courant</option></select>'+
                ' <button type="button" onclick="this.parentElement.remove()">Effacer</button></div>'
            );
        }

        const selectedModules = <?php echo json_encode($modules ?? []); ?>;
        function toggleFilieres() {
            var annee = document.querySelector('input[name="annee"]:checked');
            var filieresDiv = document.getElementById('filieres');
            if (annee && (annee.value === '3ème année' || annee.value === '4ème année' || annee.value === '5ème année')) {
                filieresDiv.style.display = 'block';
            } else {
                filieresDiv.style.display = 'none';
            }
        }
        function updateModules() {
            toggleFilieres();
            var annee = document.querySelector('input[name="annee"]:checked')?.value;
            var filiere = document.querySelector('input[name="filiere"]:checked')?.value;
            var modulesByAnnee = {
                "1ère année": ["Analyse1","Algebre1","Physique1","Mecanique1","Info1","LC1"],
                "2ème année": ["Analyse2","Algebre2","Physique2","Mecanique2","Info2","LC2"],
                "3ème année": {
                    "GI": ["Dev Web","Struct. en C","POO Java","OS","Réseau Info"],
                    "GM": ["Auto. Indus.","FAO","Méc. Mat.","Robotique","CAO"],
                    "SCM": ["Gest. Stocks","Log. & Transp.","Opt. Supply","Gest. Prod.","Modél. Proc."],
                    "GC": ["Mat. Const.","Génie Hydro.","Struct. Bét","Topo.","RDM","Gest. Chantier"],
                    "GSTR": ["Mkt Digital","Gest. Entrep.","Fin. & Compt.","Droit Aff.","Strat. Entrep."],
                    "BDIA": ["ML","Deep Learn.","Data Anal.","NLP","Vision IA","Éthique IA"]
                },
                "4ème année": {
                    "GI": ["Dev Web Av.","JEE","tech. .net","BD","Genie Logiciel","ML"],
                    "GM": ["Mécatronique","Main. Indus.","Conc. & Simul.","CND","Sys. Énerg."],
                    "SCM": ["Gest. Risques","Transp. & Dist.","Anal. Demande","Lean Mgmt","SI Log."],
                    "GC": ["Const. Métal.","Énergie & Bât.","Géotech.","Ponts & Ouvr.","Dyn. Struct."],
                    "GSTR": ["Mgmt Inno.","GRH","Gest. Qualité","Com. Intl."],
                    "BDIA": ["IA Santé","Data Sci.","Opt. & RO","Réseaux Neur.","Sécu. Data"]
                },
                "5ème année": {
                    "GI": ["Sécu. App.","ERP","Deep Learning","Frameworks Web","Dev API"],
                    "GM": ["Énergies Renouv.","Gest. Main.","Simul. Num.","Mat. Innov.","Ing. Prod"],
                    "SCM": ["E-com. & Log.","Supply Vert","SI Log.","Strat. & Pilotage"],
                    "GC": ["Éco-const.","Bât. Intell.","Transp. Urb.","Résil. Infra."],
                    "GSTR": ["Proj. Agile","Strat. Dev.","Mgmt Change.","Inno. & Tech."],
                    "BDIA": ["Big Data Fin.","Vision IA+","Modél. Préd.","RA & IA","Gouv."]
                }
            };
            var container = document.getElementById('modules-container');
            container.innerHTML = '';
            if (annee) {
                let availableModules = [];
                if (annee === '1ère année' || annee === '2ème année') {
                    availableModules = modulesByAnnee[annee] || [];
                } else if (filiere) {
                    availableModules = (modulesByAnnee[annee] || {})[filiere] || [];
                }
                var table = document.createElement('table');
                var row = document.createElement('tr');
                availableModules.forEach(function(module){
                    var cell = document.createElement('td');
                    var isChecked = selectedModules.includes(module) ? 'checked' : '';
                    cell.innerHTML = '<input type="checkbox" id="'+module+'" name="modules[]" value="'+module+'" '+isChecked+'> '+
                                     '<label for="'+module+'">'+module+'</label>';
                    row.appendChild(cell);
                });
                table.appendChild(row);
                container.appendChild(table);
            }
        }
        document.addEventListener('DOMContentLoaded', function(){
            toggleFilieres();
            updateModules();
            document.querySelectorAll('input[name="annee"]').forEach(function(el){ el.addEventListener('change', updateModules); });
            document.querySelectorAll('input[name="filiere"]').forEach(function(el){ el.addEventListener('change', updateModules); });
        });
    </script>
</body>
</html>


