<?php
require "db.php";
require "auth_check.php";
$id = $_GET['id'] ?? null;
$client = $id ? fetchOne($conn, "SELECT * FROM clients WHERE id=?", "i", [$id]) : null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone']; $addr = $_POST['address'];
    if($_POST['id']){
        $stmt = $conn->prepare("UPDATE clients SET name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi",$name,$email,$phone,$addr,$_POST['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO clients (name,email,phone,address) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss",$name,$email,$phone,$addr);
        $stmt->execute();
    }
    header("Location: clients.php");
    exit;
}
?>
<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><title>Client</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <h4><?= $client ? "Éditer client" : "Nouveau client" ?></h4>
  <form method="post" class="card p-3">
    <input type="hidden" name="id" value="<?= $client['id'] ?? '' ?>">
    <div class="mb-3"><label>Nom</label><input class="form-control" name="name" required value="<?= $client['name'] ?? '' ?>"></div>
    <div class="mb-3"><label>Email</label><input class="form-control" name="email" value="<?= $client['email'] ?? '' ?>"></div>
    <div class="mb-3"><label>Téléphone</label><input class="form-control" name="phone" value="<?= $client['phone'] ?? '' ?>"></div>
    <div class="mb-3"><label>Adresse</label><textarea class="form-control" name="address"><?= $client['address'] ?? '' ?></textarea></div>
    <button class="btn btn-success">Enregistrer</button>
    <a class="btn btn-secondary" href="clients.php">Annuler</a>
  </form>
</div>
</body></html>
