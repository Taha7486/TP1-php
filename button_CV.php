<?php
session_start();
require('fpdf.php');

// Accepter données soit depuis POST direct, soit depuis la session (formulaire unifié)
$nom       = $_POST['nom'] ?? ($_SESSION['nom'] ?? '');
$prenom    = $_POST['prenom'] ?? ($_SESSION['prenom'] ?? '');
$email     = $_POST['email'] ?? ($_SESSION['email'] ?? '');
$tel_num   = $_POST['tel_num'] ?? ($_SESSION['tel_num'] ?? '');
$adresse   = $_POST['adresse'] ?? ($_SESSION['adresse'] ?? '');
$ville     = $_POST['ville'] ?? ($_SESSION['ville'] ?? '');
$linkedin  = $_POST['linkedin'] ?? ($_SESSION['linkedin'] ?? '');

$formations   = $_POST['formations'] ?? ($_SESSION['formations'] ?? []);
$stages       = $_POST['stages'] ?? ($_SESSION['stages'] ?? []);
$competences  = $_POST['competences'] ?? ($_SESSION['competences'] ?? []);
$langues_cv   = $_POST['langues'] ?? ($_SESSION['langues_cv'] ?? []);
$centres      = $_POST['centres'] ?? ($_SESSION['centres'] ?? []);

$photo = $_SESSION['photo'] ?? '';
if (empty($photo) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads_photo/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $destPath = $uploadDir . $fileName;
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $photo = $destPath;
    }
}

// Génération du PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

if (!empty($photo)) {
    $pdf->Image($photo, 150, 10, 40, 40);
}

$pdf->Cell(0, 10, ("$nom $prenom"), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Email: $email", 0, 1);
$pdf->Cell(0, 10, "Telephone: $tel_num", 0, 1);
$pdf->Cell(0, 10, ("Adresse: $adresse, $ville"), 0, 1);
$pdf->Cell(0, 10, "LinkedIn: $linkedin", 0, 1);

$pdf->Ln(5);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

if (!empty($formations['titre'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ("Formations"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($formations['titre'] as $i => $titre) {
        $desc = $formations['description'][$i] ?? '';
        $pdf->MultiCell(0, 8, "- $titre : $desc");
    }
    $pdf->Ln(5);
}

if (!empty($stages['poste'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ("Experiences professionnelles"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($stages['poste'] as $i => $poste) {
        $entreprise = $stages['entreprise'][$i] ?? '';
        $pdf->MultiCell(0, 8, "- $poste chez $entreprise");
    }
    $pdf->Ln(5);
}

if (!empty($competences)) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ("Competences"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($competences as $c) {
        $pdf->Cell(0, 8, "- $c", 0, 1);
    }
    $pdf->Ln(5);
}

if (!empty($langues_cv['nom'])) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ("Langues"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($langues_cv['nom'] as $i => $langue) {
        $niveau = $langues_cv['niveau'][$i] ?? '';
        $pdf->Cell(0, 8, "- $langue ($niveau)", 0, 1);
    }
    $pdf->Ln(5);
}

if (!empty($centres)) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ("Centres d'interet"), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach ($centres as $c) {
        $pdf->Cell(0, 8, "- $c", 0, 1);
    }
    $pdf->Ln(5);
}

$pdf->Output("I", "CV_" . preg_replace('/[^A-Za-z0-9_-]+/', '', $nom . '_' . $prenom) . ".pdf");
