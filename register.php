<?php
session_start();

try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        if (registerUser($db, $username, $password)) {
            $_SESSION['username'] = $username;
            header("Location: tools.php");
            exit();
        } else {
            $message = "Error: Username already exists! Please choose another.";
        }
    } else {
        $message = "All fields are required!";
    }
}

function registerUser($db, $username, $password) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return false;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    return $stmt->execute([$username, $hashedPassword]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-container {
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
            .register-container {
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

    <div class="register-container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Username" required=""><br>
            <input type="password" name="password" placeholder="Password" required=""><br>
            <button type="submit">Register</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <p>Already have an account? <a href="https://skidders.xyz/login.php">Login here</a></p>
    </div>

</body>
</html>
