<?php
require "db.php";
require "auth_check.php";
$clients = fetchAll($conn, "SELECT * FROM clients");
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Clients</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h4>Clients <a class="btn btn-sm btn-success" href="client_form.php">+ Nouveau</a> <a class="btn btn-sm btn-secondary" href="employee.php">Retour</a></h4>
  <table class="table table-striped">
    <thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($clients as $c): ?>
        <tr>
          <td><?=htmlspecialchars($c['name'])?></td>
          <td><?=htmlspecialchars($c['email'])?></td>
          <td><?=htmlspecialchars($c['phone'])?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="client_form.php?id=<?=$c['id']?>">Éditer</a>
            <a class="btn btn-sm btn-outline-danger" href="client_delete.php?id=<?=$c['id']?>" onclick="return confirm('Supprimer ?')">Suppr</a>
          </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
</body>
</html>
