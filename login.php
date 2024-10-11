<?php
session_start();

try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errorMessage = "Token di sicurezza non valido!";
    } elseif ($_SESSION['login_attempts'] >= 5) {
        $errorMessage = "Troppi tentativi falliti. Riprova più tardi.";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userId = loginUser($db, $username, $password);
        if ($userId) {
            $_SESSION['user_id'] = $userId; 
            header('Location: tools.php'); 
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $errorMessage = "Username o password errati!";
        }
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

function loginUser($db, $username, $password) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user['id']; 
    }
    return false; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #000000, #1a1a1a);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            background: rgba(48, 47, 47, 0.1);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.7);
            text-align: center;
            position: relative;
        }

        h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #e0e0e0;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: none;
            border-radius: 5px;
            background: #1a1a1a;
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.1);
            transition: background 0.3s ease;
        }

        input:focus {
            background: #333;
            outline: none;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background: linear-gradient(135deg, #333333, #4c4c4c);
            color: #fff;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.3s;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }

        button:hover {
            background: linear-gradient(135deg, #555555, #777777);
            transform: translateY(-2px);
        }

        .message {
            color: #ff5252;
            margin-top: 10px;
        }

        p {
            margin-top: 40px;
        }

        a {
            color: #ffffff;
            text-decoration: none;
            border-bottom: 1px solid #e0e0e0;
            transition: border-bottom 0.3s ease;
        }

        a:hover {
            border-bottom: 1px solid #9c9e9c;
        }

        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 30px;
            }

            h2 {
                font-size: 2rem;
            }

            button {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input type="text" name="username" placeholder="Username" required=""><br>
            <input type="password" name="password" placeholder="Password" required=""><br>
            <button type="submit">Login</button>
        </form>

        <?php if ($errorMessage): ?>
            <div class="message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <p>Don’t have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>
