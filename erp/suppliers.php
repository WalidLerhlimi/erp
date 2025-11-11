<?php
require "db.php";

// Fetch suppliers
$suppliers = fetchAll($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Gestion des Fournisseurs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2>Liste des Fournisseurs</h2>

<a class="btn btn-success mb-3" href="supplier_form.php">+ Ajouter un fournisseur</a>
<a class="btn btn-secondary mb-3" href="dashboard.php">↩ Retour Dashboard</a>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Contact</th>
    <th>Téléphone</th>
    <th>Email</th>
    <th>Adresse</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($suppliers as $s): ?>
<tr>
    <td><?= $s['id'] ?></td>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= htmlspecialchars($s['contact_name']) ?></td>
    <td><?= htmlspecialchars($s['phone']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
    <td><?= htmlspecialchars($s['address']) ?></td>
    <td>
        <a class="btn btn-sm btn-primary" href="supplier_form.php?id=<?= $s['id'] ?>">Modifier</a>
        <a class="btn btn-sm btn-danger" href="supplier_delete.php?id=<?= $s['id'] ?>" onclick="return confirm('Supprimer ce fournisseur ?')">
            Supprimer
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
