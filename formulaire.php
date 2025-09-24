<?php
session_start();

// Récupération des données de session pour pré-remplissage
if (isset($_GET['value'])) {
    $nom = isset($_SESSION['nom']) ? $_SESSION['nom'] : '';
    $prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
    $age = isset($_SESSION['age']) ? $_SESSION['age'] : '';
    $tel_num = isset($_SESSION['tel_num']) ? $_SESSION['tel_num'] : '';
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $annee = isset($_SESSION['annee']) ? $_SESSION['annee'] : '';
    $filiere = isset($_SESSION['filiere']) ? $_SESSION['filiere'] : '';
    $langues = isset($_SESSION['langues']) ? $_SESSION['langues'] : [];
    $centres = isset($_SESSION['centres']) ? $_SESSION['centres'] : [];
    $projects = isset($_SESSION['projects']) ? $_SESSION['projects'] : [];
    $modules = isset($_SESSION['modules']) ? $_SESSION['modules'] : [];
    $remarque = isset($_SESSION['remarques']) ? $_SESSION['remarques'] : '';
    $document = isset($_SESSION['document']) ? $_SESSION['document'] : '';

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Renseignements</title>
</head>
<body>
    <h2>Fiche de Renseignements</h2>
    <hr>
    
    <form action="recap.php" method="post" enctype="multipart/form-data">
        <h3>Renseignements Personnels</h3>
        <table>
            <tr>
                <td><label for="nom">Nom :</label></td>
                <td><input type="text" id="nom" name="nom" value="<?php echo isset($_GET['value']) ? $nom : ''; ?>" required></td>
            </tr>
            <tr>
                <td><label for="prenom">Prénom :</label></td>
                <td><input type="text" id="prenom" name="prenom" value="<?php echo isset($_GET['value']) ? $prenom : ''; ?>" required></td>
            </tr>
            <tr>
                <td><label for="age">Âge :</label></td>
                <td><input type="number" id="age" name="age" value="<?php echo isset($_GET['value']) ? $age : ''; ?>" required min="0"></td>
            </tr>
            <tr>
                <td><label for="tel_num">Numéro de Téléphone :</label></td>
                <td><input type="tel" id="tel_num" name="tel_num" value="<?php echo isset($_GET['value']) ? $tel_num : ''; ?>" required pattern="[0-9]{10}"></td>
            </tr>
            <tr>
                <td><label for="email">Email :</label></td>
                <td><input type="email" id="email" name="email" value="<?php echo isset($_GET['value']) ? $email : ''; ?>" required></td>
            </tr>
        </table>
        <hr>

        <h3>Renseignements Académiques</h3>

        <h4>Vous êtes en :</h4>
        <table>
            <tr>
                <td>
                    <input type="radio" id="1" name="annee" value="1ère année" onchange="updateModules()" <?php echo isset($annee) && $annee == '1ère année' ? 'checked' : ''; ?> required>
                    <label for="1">1ère année</label>
                </td>
                <td>
                    <input type="radio" id="2" name="annee" value="2ème année" onchange="updateModules()" <?php echo isset($annee) && $annee == '2ème année' ? 'checked' : ''; ?> required>
                    <label for="2">2ème année</label>
                </td>
                <td>
                    <input type="radio" id="3" name="annee" value="3ème année" onchange="updateModules()" <?php echo isset($annee) && $annee == '3ème année' ? 'checked' : ''; ?> required>
                    <label for="3">3ème année</label>
                </td>
                <td>
                    <input type="radio" id="4" name="annee" value="4ème année" onchange="updateModules()" <?php echo isset($annee) && $annee == '4ème année' ? 'checked' : ''; ?> required>
                    <label for="4">4ème année</label>
                </td>
                <td>
                    <input type="radio" id="5" name="annee" value="5ème année" onchange="updateModules()" <?php echo isset($annee) && $annee == '5ème année' ? 'checked' : ''; ?> required>
                    <label for="5">5ème année</label>
                </td>
            </tr>
        </table>
        
        <!-- Filieres -->
        <div id="filieres" style="display: none;">
            <h4>Fillière :</h4>
            <table>
                <tr>
                    <td>
                        <input type="radio" name="filiere" value="GSTR" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'GSTR' ? 'checked' : ''; ?>>
                        <label for="GSTR">GSTR</label>
                    </td>
                    <td>
                        <input type="radio" name="filiere" value="GI" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'GI' ? 'checked' : ''; ?>>
                        <label for="GI">GI</label>
                    </td>
                    <td>
                        <input type="radio" name="filiere" value="SCM" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'SCM' ? 'checked' : ''; ?>>
                        <label for="SCM">SCM</label>
                    </td>
                    <td>
                        <input type="radio" name="filiere" value="GC" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'GC' ? 'checked' : ''; ?>>
                        <label for="GC">GC</label>
                    </td>
                    <td>
                        <input type="radio" name="filiere" value="GM" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'GM' ? 'checked' : ''; ?>>
                        <label for="GM">GM</label>
                    </td>
                    <td>
                        <input type="radio" name="filiere" value="BDIA" onchange="updateModules()" <?php echo isset($filiere) && $filiere == 'BDIA' ? 'checked' : ''; ?>>
                        <label for="BDIA">BDIA</label>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Modules dynamiques -->
        <h4>Modules suivis cette année :</h4>
        <div id="modules-container">
            <!-- js-->
        </div>

        <hr>

        <!-- Section Remarques -->
        <h3>Vos remarques</h3>
        <textarea name="remarques" rows="5" cols="60"><?php echo isset($_GET['value']) ? $remarque : ''; ?></textarea><br>

        <h4>Fichier Uploadé :</h4>
        <?php if (isset($_GET['value']) && !empty($document)): ?>
        <?php if (file_exists($document)): ?>
            <p>
            Fichier actuel : 
            <a href="<?= htmlspecialchars($document); ?>" target="_blank"><?= htmlspecialchars(basename($document)); ?></a>
            </p>
        <p>Si vous voulez le remplacer, choisissez un nouveau fichier :</p>
        <?php else: ?>
        <p>Aucun fichier trouvé.</p>
        <?php endif; ?>
        <?php endif; ?>

<input type="file" name="document"><br>

        <?php if (!isset($_GET['value'])) : ?>
            <button type="button" onclick="showAdditionalFields()">Suivant</button>
            <button type="reset">Réinitialiser</button>
        <?php endif; ?>

        <!-- Section Informations Complémentaires (affichage conditionnel) -->
        <div id="additionalFields" <?php if (!isset($_GET['value'])) echo 'style="display: none;"'; ?>>
            <hr>
            <h3>Informations Complémentaires</h3>

            <!-- Projets réalisés -->
            <h4>Projets réalisés :</h4>
            <div id="projects-container">
                <?php if (isset($_GET['value']) && !empty($projects)) : ?>
                    <?php foreach ($projects as $index => $project) : ?>
                        <div style="margin-bottom: 5px;">
                            <input type="text" name="projects[]" placeholder="Nom du projet" value="<?php echo $project; ?>" style="width: 300px;">
                            <?php if ($index > 0) : ?>
                                <button type="button" onclick="this.parentElement.remove()">Effacer</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div style="margin-bottom: 5px;">
                        <input type="text" name="projects[]" placeholder="Nom du projet" style="width: 300px;">
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" onclick="addField('projects-container', 'projects', 'Nom du projet')">Ajouter un projet</button><br><br>

            <!-- Centres d'intérêt -->
            <h4>Vos centres d'intérêt :</h4>
            <div id="centres-container">
                <?php if (isset($_GET['value']) && !empty($centres)) : ?>
                    <?php foreach ($centres as $index => $centre) : ?>
                        <div style="margin-bottom: 5px;">
                            <input type="text" name="centres[]" placeholder="Centres d'intérêt" value="<?php echo $centre; ?>" style="width: 300px;">
                            <?php if ($index > 0) : ?>
                                <button type="button" onclick="this.parentElement.remove()">Effacer</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div style="margin-bottom: 5px;">
                        <input type="text" name="centres[]" placeholder="Centres d'intérêt" style="width: 300px;">
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" onclick="addField('centres-container', 'centres', 'Centres d\'intérêt')">Ajouter un centre d'intérêt</button><br><br>

            <!-- Langues parlées -->
            <h4>Langues parlées :</h4>
            <div id="langues-container">
                <?php if (isset($_GET['value']) && !empty($langues)) : ?>
                    <?php foreach ($langues as $index => $langue) : ?>
                        <div style="margin-bottom: 5px;">
                            <input type="text" name="langues[]" placeholder="Langues parlées" value="<?php echo $langue; ?>" style="width: 300px;">
                            <?php if ($index > 0) : ?>
                                <button type="button" onclick="this.parentElement.remove()">Effacer</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div style="margin-bottom: 5px;">
                        <input type="text" name="langues[]" placeholder="Langues parlées" style="width: 300px;">
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" onclick="addField('langues-container', 'langues', 'Langues parlées')">Ajouter une langue</button><br><br>

            <input type="submit" name="envoyer" value="Envoyer">
            <input type="reset" value="Effacer">
        </div>
    </form>

    <script>
        
        function showAdditionalFields() {
            document.getElementById('additionalFields').style.display = 'block';
            document.querySelector('button[onclick="showAdditionalFields()"]').style.display = 'none';
        }

        function addField(containerId, inputName, placeholder) {
            const container = document.getElementById(containerId);
            const div = document.createElement("div");
            div.style.marginBottom = "5px";
            div.innerHTML = `
                <input type="text" name="${inputName}[]" placeholder="${placeholder}" style="width: 300px;">
                <button type="button" onclick="this.parentElement.remove()">Effacer</button>
            `;
            container.appendChild(div);
        }

        // Fonction pour gérer l'affichage des filières
        function toggleFilieres() {
            var annee = document.querySelector('input[name="annee"]:checked');
            var filieresDiv = document.getElementById('filieres');
            
            if (annee && (annee.value === '3ème année' || annee.value === '4ème année' || annee.value === '5ème année')) {
                filieresDiv.style.display = 'block';
            } else {
                filieresDiv.style.display = 'none';
            }
        }

        // Fonction pour mettre à jour les modules dynamiquement
        const selectedModules = <?php echo json_encode($modules ?? []); ?>;

        function updateModules() {
            toggleFilieres();
            
            var annee = document.querySelector('input[name="annee"]:checked')?.value;
            var filiere = document.querySelector('input[name="filiere"]:checked')?.value;
            
            var modulesByAnnee = {
                "1ère année": ["Analyse1", "Algebre1", "Physique1", "Mecanique1", "Info1", "LC1"],
                "2ème année": ["Analyse2", "Algebre2", "Physique2", "Mecanique2", "Info2", "LC2"],
                "3ème année": {
                    "GI": ["Dev Web", "Struct. en C", "POO Java", "OS", "Réseau Info"],
                    "GM": ["Auto. Indus.", "FAO", "Méc. Mat.", "Robotique", "CAO"],
                    "SCM": ["Gest. Stocks", "Log. & Transp.", "Opt. Supply", "Gest. Prod.", "Modél. Proc."],
                    "GC": ["Mat. Const.", "Génie Hydro.", "Struct. Bét", "Topo.", "RDM", "Gest. Chantier"],
                    "GSTR": ["Mkt Digital", "Gest. Entrep.", "Fin. & Compt.", "Droit Aff.", "Strat. Entrep."],
                    "BDIA": ["ML", "Deep Learn.", "Data Anal.", "NLP", "Vision IA", "Éthique IA"]
                },
                "4ème année": {
                    "GI": ["Dev Web Av.", "JEE", "tech. .net", "BD", "Genie Logiciel", "ML"],
                    "GM": ["Mécatronique", "Main. Indus.", "Conc. & Simul.", "CND", "Sys. Énerg."],
                    "SCM": ["Gest. Risques", "Transp. & Dist.", "Anal. Demande", "Lean Mgmt", "SI Log."],
                    "GC": ["Const. Métal.", "Énergie & Bât.", "Géotech.", "Ponts & Ouvr.", "Dyn. Struct."],
                    "GSTR": ["Mgmt Inno.", "GRH", "Gest. Qualité", "Com. Intl."],
                    "BDIA": ["IA Santé", "Data Sci.", "Opt. & RO", "Réseaux Neur.", "Sécu. Data"]
                },
                "5ème année": {
                    "GI": ["Sécu. App.", "ERP", "Deep Learning", "Frameworks Web", "Dev API"],
                    "GM": ["Énergies Renouv.", "Gest. Main.", "Simul. Num.", "Mat. Innov.", "Ing. Prod"],
                    "SCM": ["E-com. & Log.", "Supply Vert", "SI Log.", "Strat. & Pilotage"],
                    "GC": ["Éco-const.", "Bât. Intell.", "Transp. Urb.", "Résil. Infra."],
                    "GSTR": ["Proj. Agile", "Strat. Dev.", "Mgmt Change.", "Inno. & Tech."],
                    "BDIA": ["Big Data Fin.", "Vision IA+", "Modél. Préd.", "RA & IA", "Gouv."]
                }
            };

            var container = document.getElementById("modules-container");
            container.innerHTML = "";

            if (annee) {
                let availableModules = [];

                if (annee === "1ère année" || annee === "2ème année") {
                    availableModules = modulesByAnnee[annee] || [];
                } else if (filiere) {
                    availableModules = modulesByAnnee[annee]?.[filiere] || [];
                }

                // Créer un tableau pour afficher les modules
                var table = document.createElement("table");
                var row = document.createElement("tr");
                
                availableModules.forEach((module, index) => {
                    var cell = document.createElement("td");
                    var isChecked = selectedModules.includes(module) ? 'checked' : '';
                    cell.innerHTML = `
                        <input type="checkbox" id="${module}" name="modules[]" value="${module}" ${isChecked}>
                        <label for="${module}">${module}</label>
                    `;
                    row.appendChild(cell);
                });
                
                table.appendChild(row);
                container.appendChild(table);
            }
        }

        // Initialisation
        document.addEventListener("DOMContentLoaded", function() {
            toggleFilieres();
            updateModules();
            
            // Ajouter les écouteurs d'événements
            document.querySelectorAll('input[name="annee"]').forEach(function(input) {
                input.addEventListener('change', updateModules);
            });
            
            document.querySelectorAll('input[name="filiere"]').forEach(function(input) {
                input.addEventListener('change', updateModules);
            });
        });
    </script>
</body>
</html>