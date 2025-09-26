<?php
function get_pdo() {
    $host = "localhost";
    $dbname = "cv_db";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE TABLE IF NOT EXISTS cv (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) UNIQUE,
            nom VARCHAR(100),
            prenom VARCHAR(100),
            telephone VARCHAR(20),
            adresse VARCHAR(255),
            ville VARCHAR(100),
            linkedin VARCHAR(255),
            photo VARCHAR(255),
            formations TEXT,
            stages TEXT,
            competences TEXT,
            langues TEXT,
            centres TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
        $pdo->exec($sql);
        return $pdo;

    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
