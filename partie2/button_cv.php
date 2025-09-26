<?php
session_start();
require('fpdf.php');
require('cv_db.php');

$nom       = $_POST['nom'] ?? '';
$prenom    = $_POST['prenom'] ?? '';
$email     = $_POST['email'] ?? '';
$tel_num   = $_POST['tel_num'] ?? '';
$adresse   = $_POST['adresse'] ?? '';
$ville     = $_POST['ville'] ?? '';
$linkedin  = $_POST['linkedin'] ?? '';

$formations   = $_POST['formations'] ?? []; 
$stages       = $_POST['stages'] ?? [];     
$competences  = $_POST['competences'] ?? []; 
$langues_cv   = $_POST['langues'] ?? [];     
$centres      = $_POST['centres'] ?? [];     

$photo = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads_photo/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $destPath = $uploadDir . $fileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $photo = $destPath;
    }
}

// Sauvegarde du cv dans la BD
$pdo = get_pdo();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM cv WHERE email = :email");
$stmt->execute([':email' => $email]);
$emailExists = $stmt->fetchColumn();

if ($emailExists) {
    die("Erreur : un CV avec cet email existe déjà !");
}

$stmt = $pdo->prepare("
    INSERT INTO cv (email, nom, prenom, telephone, adresse, ville, linkedin, photo, formations, stages, competences, langues, centres)
    VALUES (:email, :nom, :prenom, :telephone, :adresse, :ville, :linkedin, :photo, :formations, :stages, :competences, :langues, :centres)
");

$stmt->execute([
    ':email'       => $email,
    ':nom'         => $nom,
    ':prenom'      => $prenom,
    ':telephone'   => $tel_num,
    ':adresse'     => $adresse,
    ':ville'       => $ville,
    ':linkedin'    => $linkedin,
    ':photo'       => $photo,
    ':formations'  => json_encode($formations, JSON_UNESCAPED_UNICODE),
    ':stages'      => json_encode($stages, JSON_UNESCAPED_UNICODE),
    ':competences' => json_encode($competences, JSON_UNESCAPED_UNICODE),
    ':langues'     => json_encode($langues_cv, JSON_UNESCAPED_UNICODE),
    ':centres'     => json_encode($centres, JSON_UNESCAPED_UNICODE)
]);

// Génération du PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

if (!empty($photo)) {
    $pdf->Image($photo, 150, 10, 40, 40);
}

$pdf->Cell(0, 10, utf8_decode("$nom $prenom"), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Email: $email", 0, 1);
$pdf->Cell(0, 10, "Telephone: $tel_num", 0, 1);
$pdf->Cell(0, 10, utf8_decode("Adresse: $adresse, $ville"), 0, 1);
$pdf->Cell(0, 10, "LinkedIn: $linkedin", 0, 1);

$pdf->Ln(5);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

if (!empty($formations['titre'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Formations"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($formations['titre'] as $i => $titre) {
        $desc = $formations['description'][$i] ?? '';
        $pdf->MultiCell(0, 8, "- $titre : $desc");
    }
    $pdf->Ln(5);
}

if (!empty($stages['poste'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Experiences professionnelles"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($stages['poste'] as $i => $poste) {
        $entreprise = $stages['entreprise'][$i] ?? '';
        $pdf->MultiCell(0, 8, "- $poste chez $entreprise");
    }
    $pdf->Ln(5);
}

if (!empty($competences)) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Competences"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($competences as $c) {
        $pdf->Cell(0, 8, "- $c", 0, 1);
    }
    $pdf->Ln(5);
}

if (!empty($langues_cv['nom'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Langues"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($langues_cv['nom'] as $i => $langue) {
        $niveau = $langues_cv['niveau'][$i] ?? '';
        $pdf->Cell(0, 8, "- $langue ($niveau)", 0, 1);
    }
    $pdf->Ln(5);
}

if (!empty($centres)) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode("Centres d'interet"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($centres as $c) {
        $pdf->Cell(0, 8, "- $c", 0, 1);
    }
    $pdf->Ln(5);
}

$pdf->Output("I", "CV_$nom$prenom.pdf");
