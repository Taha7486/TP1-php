<?php
session_start();
require_once __DIR__ . '/fiche_db.php';

// Simple CSRF-like check could be added if sessions contain admin flag.

$email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'admin.php';

if ($email === '' || $status === '') {
    header('Location: ' . $redirect);
    exit;
}

// Whitelist statuses
$allowed = ["Nouveau","En cours","Accepté","Refusé"];
if (!in_array($status, $allowed, true)) {
    header('Location: ' . $redirect);
    exit;
}

try {
    ensure_schema();
    $pdo = get_pdo();
    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE email = :email");
    $stmt->execute([':status' => $status, ':email' => $email]);
} catch (Throwable $e) {
    // Optionally flash error in session
}

header('Location: ' . $redirect . (strpos($redirect, '?') === false ? ('?email=' . urlencode($email)) : ''));
exit;
?>



