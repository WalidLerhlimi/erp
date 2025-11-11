<?php
require "db.php";

// only admin access
if ($_SESSION['role_name'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$employees = fetchAll($conn, "
    SELECT u.id, u.username, u.email, r.name AS role
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE r.name != 'client'
    ORDER BY u.username
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion employés</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h3>👨‍💼 Gestion des employés</h3>
    <a class="btn btn-success btn-sm mb-3" href="user_form.php">+ Nouvel employé</a>
    <a class="btn btn-secondary btn-sm mb-3" href="dashboard.php">⬅ Retour Dashboard</a>

    <table class="table table-striped">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th style="width:180px">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['username']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['role']) ?></td>
                <td>
                    <a class="btn btn-primary btn-sm" href="user_form.php?id=<?= $e['id'] ?>">Modifier</a>
                    <a class="btn btn-danger btn-sm" 
                       href="user_delete.php?id=<?= $e['id'] ?>" 
                       onclick="return confirm('Supprimer cet employé ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

</body>
</html>
