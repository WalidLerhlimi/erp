<?php
// login.php
require_once "db.php";

$message = "";

if (isset($_POST['login'])) {
    $input = trim($_POST['input']); // username ou email
    $password_input = trim($_POST['password']);

    // Récupérer l'utilisateur par username ou email
    $sql = "SELECT u.*, r.name AS role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ? OR u.email = ?";
    $user = fetchOne($conn, $sql, "ss", [$input, $input]);

    if ($user) {
        // Vérification du mot de passe (en clair ici, sinon utiliser password_verify)
        if ($password_input === $user['password_hash']) {

            // Stocker les infos en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];

            // Redirection selon le rôle
            $role = strtolower($user['role_name']);
            if ($role === 'client') {
                header("Location: client_products.php");
            } elseif ($role === 'employee') {
                header("Location: employee.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;

        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Utilisateur non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 25px;
            color: #4e54c8;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #4e54c8;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .login-container button:hover {
            background-color: #3b3fc1;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if($message != ""): ?>
            <div class="error-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="input" placeholder="Nom d'utilisateur ou Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="login">Se connecter</button>
        </form>
    </div>
</body>
</html>
