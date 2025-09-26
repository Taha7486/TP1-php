<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire CV</title>
</head>
<body>

<h1>Complétez votre CV</h1>

<form action="button_CV.php" method="post" enctype="multipart/form-data">

    <h2>Informations personnelles</h2>
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required><br>

    <label for="prenom">Prenom :</label>
    <input type="text" id="prenom" name="prenom" required><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required><br>

    <label for="tel_num">Telephone :</label>
    <input type="text" id="tel_num" name="tel_num" required><br>

    <label for="adresse">Adresse :</label>
    <input type="text" id="adresse" name="adresse" required><br>

    <label for="ville">Ville :</label>
    <input type="text" id="ville" name="ville" required><br>

    <label for="linkedin">LinkedIn :</label>
    <input type="url" id="linkedin" name="linkedin" placeholder="https://www.linkedin.com/in/votreprofil"><br>

    <label for="photo">Photo :</label>
    <input type="file" id="photo" name="photo" accept="image/*" required>
    <hr>

    <h2>Formations</h2>
    <div id="formations-container">
        <div>
            <input type="text" name="formations[titre][]" placeholder="Diplôme / Formation" required>
            <input type="text" name="formations[description][]" placeholder="Description">
        </div>
    </div>
    <button type="button" onclick="addFormation()">Ajouter une formation</button>
    <hr>

    <h2>Stages / Expériences</h2>
    <div id="stages-container">
        <div>
            <input type="text" name="stages[poste][]" placeholder="Intitule du poste" required>
            <input type="text" name="stages[entreprise][]" placeholder="Entreprise" required>
        </div>
    </div>
    <button type="button" onclick="addStage()">Ajouter un stage</button>
    <hr>

    <h2>Compétences</h2>
    <div id="competences-container">
        <div>
            <input type="text" name="competences[]" placeholder="Competence" required>
        </div>
    </div>
    <button type="button" onclick="addCompetence()">Ajouter une compétence</button>
    <hr>

    <h2>Langues</h2>
    <div id="langues-container">
        <div>
            <input type="text" name="langues[nom][]" placeholder="Langue" required>
            <select name="langues[niveau][]">
                <option value="Debutant">Debutant</option>
                <option value="Intermediaire">Intermediaire</option>
                <option value="Courant">Courant</option>
            </select>
        </div>
    </div>
    <button type="button" onclick="addLangue()">Ajouter une langue</button>
    <hr>

    <h2>Centres d'interet</h2>
    <div id="centres-container">
        <div>
            <input type="text" name="centres[]" placeholder="Centre d'interet" required>
        </div>
    </div>
    <button type="button" onclick="addCentre()">Ajouter un centre</button>
    <hr>

    <input type="submit" value="Generer mon CV">
</form>

<script>
function addFormation() {
    document.getElementById('formations-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="formations[titre][]" placeholder="Diplome / Formation" required>
            <input type="text" name="formations[description][]" placeholder="Description">
        </div>`);
}
function addStage() {
    document.getElementById('stages-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="stages[poste][]" placeholder="Intitule du poste" required>
            <input type="text" name="stages[entreprise][]" placeholder="Entreprise" required>
        </div>`);
}
function addCompetence() {
    document.getElementById('competences-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="competences[]" placeholder="Competence" required>
        </div>`);
}
function addLangue() {
    document.getElementById('langues-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="langues[nom][]" placeholder="Langue" required>
            <select name="langues[niveau][]">
                <option value="Debutant">Debutant</option>
                <option value="Intermediaire">Intermediaire</option>
                <option value="Courant">Courant</option>
            </select>
        </div>`);
}
function addCentre() {
    document.getElementById('centres-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="centres[]" placeholder="Centre d'interet" required>
        </div>`);
}
</script>

</body>
</html>
