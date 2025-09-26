<?php
session_start();
require_once __DIR__ . '/fiche_db.php';

try {
	ensure_schema();
	$pdo = get_pdo();
    $stmt = $pdo->query("SELECT email, nom, prenom, filiere, annee, internship_type, internship_duration, status, created_at FROM users ORDER BY created_at DESC");
	$rows = $stmt->fetchAll();
} catch (Throwable $e) {
	$err = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin - Candidatures</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
	<h1>Tableau de bord - Candidatures de stage</h1>
	<?php if (!empty($err)): ?>
		<div style="color:#b00020; font-weight:bold;">Erreur: <?= htmlspecialchars($err); ?></div>
	<?php endif; ?>
	<table border="1" cellpadding="6" cellspacing="0">
		<tr>
			<th>Date</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Email</th>
			<th>Année</th>
			<th>Filière</th>
			<th>Type de stage</th>
			<th>Durée</th>
			<th>Status</th>
			<th>Actions</th>
		</tr>
		<?php if (!empty($rows)):
			foreach ($rows as $r): ?>
			<tr>
				<td><?= htmlspecialchars($r['created_at']); ?></td>
				<td><?= htmlspecialchars($r['nom']); ?></td>
				<td><?= htmlspecialchars($r['prenom']); ?></td>
				<td><?= htmlspecialchars($r['email']); ?></td>
				<td><?= htmlspecialchars($r['annee']); ?></td>
				<td><?= htmlspecialchars($r['filiere']); ?></td>
				<td><?= htmlspecialchars($r['internship_type']); ?></td>
				<td><?= htmlspecialchars($r['internship_duration']); ?></td>
			<td>
				<form action="admin_status_update.php" method="post" style="display:inline;">
					<input type="hidden" name="email" value="<?= htmlspecialchars($r['email']); ?>">
					<input type="hidden" name="redirect" value="admin.php">
					<select name="status">
						<?php foreach (["Nouveau","En cours","Accepté","Refusé"] as $s): $sel = (($r['status'] ?? '') === $s) ? 'selected' : ''; ?>
							<option value="<?= htmlspecialchars($s); ?>" <?= $sel; ?>><?= htmlspecialchars($s); ?></option>
						<?php endforeach; ?>
					</select>
					<button type="submit">Mettre à jour</button>
				</form>
			</td>
				<td>
					<form action="admin_view.php" method="get" style="display:inline;">
						<input type="hidden" name="email" value="<?= htmlspecialchars($r['email']); ?>">
						<button type="submit">Voir</button>
					</form>
					<form action="admin_cv.php" method="get" style="display:inline;">
						<input type="hidden" name="email" value="<?= htmlspecialchars($r['email']); ?>">
						<button type="submit">CV PDF</button>
					</form>
				</td>
			</tr>
		<?php endforeach; endif; ?>
	</table>
</body>
</html>


