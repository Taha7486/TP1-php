<?php
session_start();
require_once __DIR__ . '/fiche_db.php';
require_once __DIR__ . '/fpdf.php';

$email = isset($_GET['email']) ? strtolower(trim($_GET['email'])) : '';
if ($email === '') {
	header('Location: admin.php');
	exit;
}

try {
	ensure_schema();
	$pdo = get_pdo();
	$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
	$stmt->execute([':email' => $email]);
	$user = $stmt->fetch();
} catch (Throwable $e) {
	die('Erreur: ' . htmlspecialchars($e->getMessage()));
}

if (!$user) {
	die('Utilisateur introuvable');
}

// Reconstruire structures attendues par button_CV.php
$formations = json_decode($user['formations'] ?? '[]', true) ?: ['titre'=>[], 'description'=>[]];
$stages = json_decode($user['stages'] ?? '[]', true) ?: ['poste'=>[], 'entreprise'=>[]];
$competences = json_decode($user['competences'] ?? '[]', true) ?: [];
$langues_cv = json_decode($user['langues_cv'] ?? '[]', true) ?: ['nom'=>[], 'niveau'=>[]];
$centres = json_decode($user['centres'] ?? '[]', true) ?: [];
$photo = $user['photo_path'] ?? '';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
if (!empty($photo) && file_exists($photo)) {
	$pdf->Image($photo, 150, 10, 40, 40);
}
$pdf->Cell(0, 10, ($user['nom'] . ' ' . $user['prenom']), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Email: ' . $user['email'], 0, 1);
$pdf->Cell(0, 10, 'Telephone: ' . ($user['tel_num'] ?? ''), 0, 1);
$pdf->Cell(0, 10, 'Adresse: ' . (($user['adresse'] ?? '') . ', ' . ($user['ville'] ?? '')), 0, 1);
$pdf->Cell(0, 10, 'LinkedIn: ' . ($user['linkedin'] ?? ''), 0, 1);

$pdf->Ln(5);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

if (!empty($formations['titre'])) {
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(0, 10, 'Formations', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	foreach ($formations['titre'] as $i => $titre) {
		$desc = $formations['description'][$i] ?? '';
		$pdf->MultiCell(0, 8, '- ' . $titre . ' : ' . $desc);
	}
	$pdf->Ln(5);
}

if (!empty($stages['poste'])) {
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(0, 10, 'Experiences professionnelles', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	foreach ($stages['poste'] as $i => $poste) {
		$entreprise = $stages['entreprise'][$i] ?? '';
		$pdf->MultiCell(0, 8, '- ' . $poste . ' chez ' . $entreprise);
	}
	$pdf->Ln(5);
}

if (!empty($competences)) {
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(0, 10, 'Competences', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	foreach ($competences as $c) {
		$pdf->Cell(0, 8, '- ' . $c, 0, 1);
	}
	$pdf->Ln(5);
}

if (!empty($langues_cv['nom'])) {
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(0, 10, 'Langues', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	foreach ($langues_cv['nom'] as $i => $langue) {
		$niveau = $langues_cv['niveau'][$i] ?? '';
		$pdf->Cell(0, 8, '- ' . $langue . ' (' . $niveau . ')', 0, 1);
	}
	$pdf->Ln(5);
}

if (!empty($centres)) {
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(0, 10, 'Centres d\'interet', 0, 1);
	$pdf->SetFont('Arial', '', 12);
	foreach ($centres as $c) {
		$pdf->Cell(0, 8, '- ' . $c, 0, 1);
	}
	$pdf->Ln(5);
}

$pdf->Output('I', 'CV_' . preg_replace('/[^A-Za-z0-9_-]+/', '', ($user['nom'] . '_' . $user['prenom'])) . '.pdf');


