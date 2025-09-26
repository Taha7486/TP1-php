<?php
// Configuration de la connexion à la base de données (XAMPP par défaut)
$DB_HOST = '127.0.0.1';
$DB_PORT = '3306';
$DB_NAME = 'tp1';
$DB_USER = 'root';
$DB_PASS = '';

function get_pdo(): PDO {
	global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
	$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];
	return new PDO($dsn, $DB_USER, $DB_PASS, $options);
}

// Création de la base/table au besoin (idempotent)
function ensure_schema(): void {
	$pdoRoot = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]);
	$pdoRoot->exec("CREATE DATABASE IF NOT EXISTS tp1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
	$pdoRoot = null;

	$pdo = get_pdo();
	$pdo->exec(
		"CREATE TABLE IF NOT EXISTS users (
			email            VARCHAR(254) NOT NULL,
			nom              VARCHAR(100) NOT NULL,
			prenom           VARCHAR(100) NOT NULL,
			age              INT NULL,
			tel_num          VARCHAR(30) NULL,
			annee            VARCHAR(20) NULL,
			filiere          VARCHAR(20) NULL,
			langues          TEXT NULL,
			centres          TEXT NULL,
			projects         TEXT NULL,
			modules          TEXT NULL,
			remarques        TEXT NULL,
			document_path    VARCHAR(255) NULL,
			-- Champs CV supplémentaires
			adresse          VARCHAR(255) NULL,
			ville            VARCHAR(100) NULL,
			linkedin         VARCHAR(255) NULL,
			photo_path       VARCHAR(255) NULL,
			formations       TEXT NULL,
			stages           TEXT NULL,
			competences      TEXT NULL,
			langues_cv       TEXT NULL,
			-- Champs candidature de stage
			internship_type  VARCHAR(50) NULL,
			internship_duration VARCHAR(100) NULL,
			cover_letter     TEXT NULL,
			status           VARCHAR(20) NULL,
			created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (email)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
	);

	// Assurer l'ajout des colonnes si la table existait déjà
	$columns = [
		['status', 'ADD COLUMN IF NOT EXISTS status VARCHAR(20) NULL'],
		['adresse', 'ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) NULL'],
		['ville', 'ADD COLUMN IF NOT EXISTS ville VARCHAR(100) NULL'],
		['linkedin', 'ADD COLUMN IF NOT EXISTS linkedin VARCHAR(255) NULL'],
		['photo_path', 'ADD COLUMN IF NOT EXISTS photo_path VARCHAR(255) NULL'],
		['formations', 'ADD COLUMN IF NOT EXISTS formations TEXT NULL'],
		['stages', 'ADD COLUMN IF NOT EXISTS stages TEXT NULL'],
		['competences', 'ADD COLUMN IF NOT EXISTS competences TEXT NULL'],
		['langues_cv', 'ADD COLUMN IF NOT EXISTS langues_cv TEXT NULL'],
		['internship_type', 'ADD COLUMN IF NOT EXISTS internship_type VARCHAR(50) NULL'],
		['internship_duration', 'ADD COLUMN IF NOT EXISTS internship_duration VARCHAR(100) NULL'],
		['cover_letter', 'ADD COLUMN IF NOT EXISTS cover_letter TEXT NULL'],
	];
	foreach ($columns as [$name, $ddl]) {
		try {
			$pdo->exec("ALTER TABLE users $ddl");
		} catch (Throwable $e) {
			// ignore if not supported or already exists
		}
	}

	// Contrainte simple de format email via triggers (facultatif)
	try {
		// Recréer proprement avec un motif plus permissif
		$pdo->exec("DROP TRIGGER IF EXISTS users_email_validate_before_insert");
		$pdo->exec("DROP TRIGGER IF EXISTS users_email_validate_before_update");

		$pdo->exec(
			"CREATE TRIGGER users_email_validate_before_insert
			BEFORE INSERT ON users FOR EACH ROW
			BEGIN
				IF NEW.email NOT REGEXP '^[^@ ]+@[^@ ]+[.][^@ ]+$' THEN
					SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid email format';
				END IF;
			END"
		);
	} catch (Throwable $e) {
		
	}
}


