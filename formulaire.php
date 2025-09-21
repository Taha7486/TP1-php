<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP1</title>
</head>
<body>
    <form action="recap.php" method="post" enctype="multipart/form-data">
        <h2>Fiche de Renseignements</h2>
        <hr>
        <h3>Renseignements Personnels</h3>
        <table>
            <tr>
                <td>Nom :</td><td><input type="text" name="nom"></td>
            </tr>
            <tr>
                <td>Prénom :</td><td><input type="text" name="prenom"></td>
            </tr>
            <tr>
                <td>Âge :</td><td><input type="number" name="age"></td>
            </tr>
            <tr>
                <td>Numéro de Téléphone :</td><td><input type="text" name="tel_num"></td>
            </tr>
            <tr>
                <td>Email :</td><td><input type="email" name="email"></td>
            </tr>
        </table>
        <hr>
        <h3>Renseignements Académiques</h3>

        <h4>Vous êtes en :</h4>
        <table>
            <tr>
                <td><input type="radio" name="filiere" value="2AP"> 2AP</td>
                <td><input type="radio" name="filiere" value="GSTR"> GSTR</td>
                <td><input type="radio" name="filiere" value="GI"> GI</td>
                <td><input type="radio" name="filiere" value="SCM"> SCM</td>
                <td><input type="radio" name="filiere" value="GC"> GC</td>
                <td><input type="radio" name="filiere" value="MS"> MS</td>
            </tr>
        </table>

        <h4>Année :</h4>
        <table>
            <tr>
                <td><input type="radio" name="annee" value="1er annee"> 1ère année</td>
                <td><input type="radio" name="annee" value="2eme annee"> 2ème année</td>
                <td><input type="radio" name="annee" value="3eme annee"> 3ème année</td>
            </tr>
        </table>

        <h4>Modules suivis cette année :</h4>
        <table>
            <tr>
                <td><input type="checkbox" name="module[]" value="Pro Av"> Pro Av</td>
                <td><input type="checkbox" name="module[]" value="Compilation"> Compilation</td>
                <td><input type="checkbox" name="module[]" value="Reseaux Av"> Réseaux Av</td>
                <td><input type="checkbox" name="module[]" value="Web Avancee"> Web Avancée</td>
                <td><input type="checkbox" name="module[]" value="POO"> POO</td>
                <td><input type="checkbox" name="module[]" value="BD"> BD</td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Nombre de projets réalisés cette année :</td><td><input type="number" name="projet_num"></td>
            </tr>
        </table>
        <hr>
        <h3>Informations Complémentaires</h3>
        <h4>Projets et Stages réalisés :</h4>
        <textarea name="projets_stages" rows="5" cols="60" placeholder="Décrivez vos projets et stages"></textarea><br>
        <h4>Vos centres d'intérêt :</h4>
        <table>
            <tr>
                <td><input type="checkbox" name="interets[]" value="Sport"> Sport</td>
                <td><input type="checkbox" name="interets[]" value="Lecture"> Lecture</td>
                <td><input type="checkbox" name="interets[]" value="Voyage"> Voyage</td>
                <td><input type="checkbox" name="interets[]" value="Musique"> Musique</td>
                <td><input type="checkbox" name="interets[]" value="Informatique"> Informatique</td>
            </tr>
        </table>
        <h4>Langues parlées :</h4>
        <table>
            <tr>
                <td><input type="checkbox" name="langues[]" value="Français"> Français</td>
                <td><input type="checkbox" name="langues[]" value="Anglais"> Anglais</td>
                <td><input type="checkbox" name="langues[]" value="Arabe"> Arabe</td>
                <td><input type="checkbox" name="langues[]" value="Espagnol"> Espagnol</td>
            </tr>
        </table>
        <hr>
        <h3>Vos remarques</h3>
        <textarea name="commentaire" rows="5" cols="60"></textarea><br>
        <h4>Joindre un fichier :</h4>
        <input type="file" name="fichier"><br><br>
        <input type="submit" name="envoyer" value="Envoyer">
        <input type="reset" value="Effacer">
    </form>
</body>
</html>
