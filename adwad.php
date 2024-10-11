<?php

try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; 
}


function setAdminPassword($password) {
    global $db; 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $db->exec("CREATE TABLE IF NOT EXISTS admin (id INTEGER PRIMARY KEY, password TEXT NOT NULL)");


    $stmt = $db->prepare("SELECT COUNT(*) FROM admin");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn();

    if ($adminExists) {
        $stmt = $db->prepare("UPDATE admin SET password = ? WHERE id = 1");
        $stmt->execute([$hashedPassword]);
    } else {
        $stmt = $db->prepare("INSERT INTO admin (password) VALUES (?)");
        $stmt->execute([$hashedPassword]);
    }
}

setAdminPassword('');
echo "Admin password changed";
?>
