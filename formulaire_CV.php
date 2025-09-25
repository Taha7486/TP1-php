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
            <input type="text" name="formations[description][]" placeholder="Description ">
        </div>
    </div>
    <button type="button" onclick="addFormation()">Ajouter une formation</button>
    <hr>

    <h2>Stages / Expériences</h2>
    <div id="stages-container">
        <div>
            <input type="text" name="stages[poste][]" placeholder="Intitulé du poste" required>
            <input type="text" name="stages[entreprise][]" placeholder="Entreprise" required>
        </div>
    </div>
    <button type="button" onclick="addStage()">Ajouter un stage</button>
    <hr>

    <h2>Compétences</h2>
    <div id="competences-container">
        <div>
            <input type="text" name="competences[]" placeholder="Compétence" required>
        </div>
    </div>
    <button type="button" onclick="addCompetence()">Ajouter une compétence</button>
    <hr>

    <h2>Langues</h2>
    <div id="langues-container">
        <div>
            <input type="text" name="langues[nom][]" placeholder="Langue" required>
            <select name="langues[niveau][]">
                <option value="Débutant">Débutant</option>
                <option value="Intermédiaire">Intermédiaire</option>
                <option value="Courant">Courant</option>
            </select>
        </div>
    </div>
    <button type="button" onclick="addLangue()">Ajouter une langue</button>
    <hr>

    <h2>Centres d'intérêt</h2>
    <div id="centres-container">
        <div>
            <input type="text" name="centres[]" placeholder="Centre d'intérêt" required>
        </div>
    </div>
    <button type="button" onclick="addCentre()">Ajouter un centre</button>
    <hr>

    <input type="submit" value="Générer mon CV">
</form>

<script>
function addFormation() {
    document.getElementById('formations-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="formations[titre][]" placeholder="Diplôme / Formation" required>
            <input type="text" name="formations[description][]" placeholder="Description (optionnel)">
        </div>`);
}
function addStage() {
    document.getElementById('stages-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="stages[poste][]" placeholder="Intitulé du poste" required>
            <input type="text" name="stages[entreprise][]" placeholder="Entreprise" required>
        </div>`);
}
function addCompetence() {
    document.getElementById('competences-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="competences[]" placeholder="Compétence" required>
        </div>`);
}
function addLangue() {
    document.getElementById('langues-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="langues[nom][]" placeholder="Langue" required>
            <select name="langues[niveau][]">
                <option value="Débutant">Débutant</option>
                <option value="Intermédiaire">Intermédiaire</option>
                <option value="Courant">Courant</option>
            </select>
        </div>`);
}
function addCentre() {
    document.getElementById('centres-container').insertAdjacentHTML('beforeend', `
        <div>
            <input type="text" name="centres[]" placeholder="Centre d'intérêt" required>
        </div>`);
}
</script>

</body>
</html>
