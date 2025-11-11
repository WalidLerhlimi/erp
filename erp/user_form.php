<?php
require "db.php";

if ($_SESSION['role_name'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$isEdit = isset($_GET['id']);
$roles = fetchAll($conn, "SELECT * FROM roles WHERE name != 'client'");

$user = [
    "username"=>"",
    "email"=>"",
    "role_id"=>""
];

if ($isEdit) {
    $user = fetchOne($conn, "SELECT * FROM users WHERE id=?", "i", [$_GET['id']]);
    if (!$user) {
        die("Utilisateur introuvable");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $role_id = $_POST["role_id"];
    $password = $_POST["password"] ?? "";

    if ($isEdit) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role_id=?, password_hash=? WHERE id=?");
            $stmt->bind_param("ssisi", $username, $email, $role_id, $hash, $_GET['id']);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role_id=? WHERE id=?");
            $stmt->bind_param("ssii", $username, $email, $role_id, $_GET['id']);
        }
        $stmt->execute();
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users(username,email,password_hash,role_id) VALUES(?,?,?,?)");
        $stmt->bind_param("sssi", $username, $email, $hash, $role_id);
        $stmt->execute();
    }

    header("Location: users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= $isEdit ? "Modifier" : "Ajouter" ?> employé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h3><?= $isEdit ? "Modifier" : "Ajouter" ?> un employé</h3>

    <form method="post">
        <div class="mb-2">
            <label>Nom</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
        </div>

        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="mb-2">
            <label>Rôle</label>
            <select class="form-control" name="role_id">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $r['id']==$user['role_id']?'selected':'' ?>>
                        <?= $r['name'] ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Mot de passe <?= $isEdit ? "(laisser vide si inchangé)" : "" ?></label>
            <input type="password" name="password" class="form-control" <?= $isEdit ? "" : "required" ?>>
        </div>

        <button class="btn btn-primary"><?= $isEdit ? "Mettre à jour" : "Ajouter" ?></button>
        <a class="btn btn-secondary" href="users.php">Annuler</a>
    </form>
</div>

</body>
</html>
