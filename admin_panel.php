<?php

try {
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

session_start();
$errorMessage = '';
$successMessage = '';


function checkAdminPassword($db, $password) {
    $stmt = $db->prepare("SELECT password FROM admin LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($admin && password_verify($password, $admin['password']));
}


function addTool($db, $title, $description, $download_link) {
    $stmt = $db->prepare("INSERT INTO tools (title, description, download_link) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $description, $download_link]);
}


function deleteTool($db, $tool_id) {
    $stmt = $db->prepare("DELETE FROM tools WHERE id = ?");
    return $stmt->execute([$tool_id]);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_password'])) {
    $admin_password = $_POST['admin_password'];
    if (checkAdminPassword($db, $admin_password)) {
        $_SESSION['is_admin'] = true;
    } else {
        $errorMessage = "Password errata!";
    }
}


if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'], $_POST['description'], $_POST['download_link'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $download_link = $_POST['download_link'];

    if (addTool($db, $title, $description, $download_link)) {
        $successMessage = "Tool aggiunto con successo!";
    } else {
        $errorMessage = "Errore nell'aggiungere il tool.";
    }
}


if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] && isset($_POST['delete_tool_id'])) {
    $tool_id = $_POST['delete_tool_id'];

    if (deleteTool($db, $tool_id)) {
        $successMessage = "Tool eliminato con successo!";
    } else {
        $errorMessage = "Errore nell'eliminare il tool.";
    }

    
    header("Location: admin_panel.php");
    exit();
}


$tools = [];
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $stmt = $db->query("SELECT * FROM tools");
    $tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #111;
            border-radius: 10px;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            box-sizing: border-box;
            background-color: #333;
            border: none;
            border-bottom: 2px solid #fff;
            color: #fff;
            outline: none;
            transition: border-bottom 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-bottom: 2px solid #7c7b7c;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background-color: #4c4c4d;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #525252;
        }

        .message {
            color: #f00;
            text-align: center;
            margin-top: 10px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #444;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #111;
            color: #5e5e5e;
        }

        table td a {
            color: #666566;
            text-decoration: none;
        }

        table td a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>

        <?php if (!isset($_SESSION['is_admin'])): ?>
            <h2>Admin Login</h2>
            <form action="admin_panel.php" method="POST">
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <button type="submit">Login</button>
            </form>
            <p class="message"><?php echo $errorMessage; ?></p>
        <?php else: ?>
            <h2>Add Tool</h2>
            <form action="admin_panel.php" method="POST">
                <input type="text" name="title" placeholder="Title" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="text" name="download_link" placeholder="Download Link" required>
                <button type="submit">Create Tool</button>
            </form>
            <p><?php echo $successMessage; ?></p>

            <h2>Existing Tools</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Download Link</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tools as $tool): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tool['title']); ?></td>
                            <td><?php echo htmlspecialchars($tool['description']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($tool['download_link']); ?>" target="_blank">Download</a></td>
                            <td>
                                <form action="admin_panel.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_tool_id" value="<?php echo $tool['id']; ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
