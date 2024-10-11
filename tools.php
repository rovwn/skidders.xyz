<?php

try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; 
}

session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}


session_regenerate_id();

$stmt = $db->query("SELECT * FROM tools");
$tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>skidders.xyz</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #000000, #1a1a1a); /* Gradiente scuro */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            justify-items: center;
            padding: 40px;
            width: 100%;
            max-width: 1200px;
        }

        .card {
            background: rgba(48, 47, 47, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 100%;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.2);
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #e0e0e0;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
        }

        p {
            font-size: 1rem;
            margin-bottom: 20px;
            color: #b3b3b3;
            line-height: 1.5; 
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #333333, #555555);
            border-radius: 25px;
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.3s;
        }

        .button:hover {
            background: linear-gradient(135deg, #777777, #999999);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <?php foreach ($tools as $tool): ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($tool['title']); ?></h2>
                <p><?php echo htmlspecialchars($tool['description']); ?></p>
                <a href="<?php echo htmlspecialchars($tool['download_link']); ?>" class="button">Download</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
