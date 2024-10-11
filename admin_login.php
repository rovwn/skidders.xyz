<?php
// Connessione al database SQLite
try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

session_start();
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminPassword = $_POST['admin_password'];

    if (checkAdminPassword($db, $adminPassword)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_panel.php');
        exit;
    } else {
        $errorMessage = "Password errata!";
    }
}

function checkAdminPassword($db, $password) {
    $stmt = $db->query("SELECT password FROM admin LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        return password_verify($password, $row['password']);
    }
    
    return false;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
</head>
<body>
    <h2>Login Admin</h2>
    <form method="POST">
        <input type="password" name="admin_password" placeholder="Password Admin" required>
        <button type="submit">Accedi</button>
    </form>
    <p><?php echo $errorMessage; ?></p>
</body>
</html>
