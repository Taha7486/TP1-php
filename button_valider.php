<?php
session_start();

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
$document = isset($_SESSION['document']) ? basename($_SESSION['document']) : 'Non défini';

$content = "==================== FICHE DE RENSEIGNEMENTS ====================\n\n";

$content .= ">>> RENSEIGNEMENTS PERSONNELS <<<\n";
$content .= "Nom            : $nom\n";
$content .= "Prénom         : $prenom\n";
$content .= "Âge            : $age\n";
$content .= "Téléphone      : $tel_num\n";
$content .= "Email          : $email\n\n";

$content .= ">>> RENSEIGNEMENTS ACADÉMIQUES <<<\n";
$content .= "Année          : $annee\n";
$content .= "Filière        : $filiere\n";
$content .= "Modules        :\n";
foreach ($modules as $module) {
    $content .= "    - $module\n";
}

if (!empty($remarque) && $remarque !== 'Non défini') {
    $content .= "\n>>> REMARQUES <<<\n";
    $content .= "$remarque\n";
}
$content .= "\nFichier Téléchargé :\n    - $document\n";

$content .= "\n>>> INFORMATIONS COMPLÉMENTAIRES <<<\n\n";

$content .= "Projets Réalisés :\n";
foreach ($projects as $project) {
    $project = empty($project) ? "Non défini" : $project;
    $content .= "    - $project\n";
}

$content .= "Centres d'intérêt :\n";
foreach ($centres as $centre) {
    $centre = empty($centre) ? "Non défini" : $centre;
    $content .= "    - $centre\n";
}

$content .= "Langues Parlées :\n";
foreach ($langues as $langue) {
    $langue = empty($langue) ? "Non défini" : $langue;
    $content .= "    - $langue\n";
}

// Téléchargement du fichier
$fileName = "Fiche_de_Renseignements-" . $nom . "_" . $prenom . ".txt";

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . strlen($content));
echo $content;
exit;
?>
