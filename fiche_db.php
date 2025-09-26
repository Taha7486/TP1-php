<?php
function get_pdo_fiche() {
    $host = "localhost";
    $dbname = "cv_db";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE TABLE IF NOT EXISTS fiche_renseignement (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) UNIQUE,
            nom VARCHAR(100),
            prenom VARCHAR(100),
            age INT,
            telephone VARCHAR(20),
            annee VARCHAR(50),
            filiere VARCHAR(100),
            modules TEXT,
            projets TEXT,
            langues TEXT,
            centres TEXT,
            remarques TEXT,
            document VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $pdo->exec($sql);
        return $pdo;

    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
