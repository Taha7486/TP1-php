<?php
session_start();
require_once __DIR__ . '/fiche_db.php';

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
	$err = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Détail</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
	<a href="admin.php">&larr; Retour</a>
	<h1>Détail candidature</h1>
	<?php if (!empty($err)): ?>
		<div style="color:#b00020; font-weight:bold;">Erreur: <?= htmlspecialchars($err); ?></div>
	<?php endif; ?>
	<?php if (!$user): ?>
		<p>Utilisateur introuvable.</p>
	<?php else: ?>
		<table border="1" cellpadding="6" cellspacing="0">
			<tr><td><strong>Nom</strong></td><td><?= htmlspecialchars($user['nom']); ?></td></tr>
			<tr><td><strong>Prénom</strong></td><td><?= htmlspecialchars($user['prenom']); ?></td></tr>
			<tr><td><strong>Email</strong></td><td><?= htmlspecialchars($user['email']); ?></td></tr>
			<tr><td><strong>Téléphone</strong></td><td><?= htmlspecialchars($user['tel_num']); ?></td></tr>
			<tr><td><strong>Année</strong></td><td><?= htmlspecialchars($user['annee']); ?></td></tr>
			<tr><td><strong>Filière</strong></td><td><?= htmlspecialchars($user['filiere']); ?></td></tr>
			<tr><td><strong>Type de stage</strong></td><td><?= htmlspecialchars($user['internship_type']); ?></td></tr>
			<tr><td><strong>Durée</strong></td><td><?= htmlspecialchars($user['internship_duration']); ?></td></tr>
			<tr>
				<td><strong>Status</strong></td>
				<td>
					<form action="admin_status_update.php" method="post" style="display:inline;">
						<input type="hidden" name="email" value="<?= htmlspecialchars($user['email']); ?>">
						<input type="hidden" name="redirect" value="admin_view.php">
						<select name="status">
							<?php foreach (["Nouveau","En cours","Accepté","Refusé"] as $s): $sel = (($user['status'] ?? '') === $s) ? 'selected' : ''; ?>
								<option value="<?= htmlspecialchars($s); ?>" <?= $sel; ?>><?= htmlspecialchars($s); ?></option>
							<?php endforeach; ?>
						</select>
						<button type="submit">Mettre à jour</button>
					</form>
				</td>
			</tr>
			<tr><td><strong>Lettre</strong></td><td><?= nl2br(htmlspecialchars($user['cover_letter'])); ?></td></tr>
			<tr><td><strong>Remarques</strong></td><td><?= nl2br(htmlspecialchars($user['remarques'])); ?></td></tr>
		</table>
		<div style="margin-top:12px;">
			<form action="admin_cv.php" method="get" style="display:inline;">
				<input type="hidden" name="email" value="<?= htmlspecialchars($user['email']); ?>">
				<button type="submit">Ouvrir CV PDF</button>
			</form>
		</div>
	<?php endif; ?>
</body>
</html>


