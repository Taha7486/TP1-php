<?php
// Page d'accueil avec deux choix: Admin ou Formulaire
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <style>
        body { 
            margin: 0; 
            font-family: Arial, sans-serif; 
            background: #f5f7fb; 
            color: #222; 
        }
        .container { 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 24px; 
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.08);
            padding: 32px;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        h1 { 
            font-size: 22px; 
            margin: 0 0 8px; 
        }
        p { 
            margin: 0 0 24px; 
            color: #555; 
        }
        .actions { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 12px; 
        }
        @media (min-width: 480px) {
            .actions { grid-template-columns: 1fr 1fr; }
        }
        a.button {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid #d0d7de;
            background: #fff;
            color: #0d5bd7;
            transition: box-shadow .15s ease, transform .02s ease;
        }
        a.button:hover { box-shadow: 0 4px 16px rgba(13,91,215,0.15); }
        a.button:active { transform: translateY(1px); }
        .button.primary {
            background: #0d5bd7;
            color: #fff;
            border-color: #0d5bd7;
        }
    </style>
    <!-- Redirection légère si "formule.php" est demandé ailleurs -->
    <!-- Créez un fichier "formule.php" qui inclut/redirige vers "formulaire_all.php" si nécessaire. -->
    <!-- Ce fichier pointe directement vers admin.php et formulaire_all.php existants. -->
    </head>
<body>
    <div class="container">
        <div class="card">
            <h1>Bienvenue</h1>
            <p>Choisissez une option pour continuer.</p>
            <div class="actions">
                <a class="button" href="admin.php">Espace Admin</a>
                <a class="button primary" href="formule.php">Formulaire</a>
            </div>
        </div>
    </div>
</body>
</html>


