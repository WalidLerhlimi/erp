<?php
require "db.php";

$id = $_GET['id'] ?? null;
$name = $contact_name = $email = $phone = $address = "";

// Fetch supplier if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $supplier = $stmt->get_result()->fetch_assoc();
    $name = $supplier['name'];
    $contact_name = $supplier['contact_name'];
    $email = $supplier['email'];
    $phone = $supplier['phone'];
    $address = $supplier['address'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_name = $_POST['contact_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE suppliers SET name=?, contact_name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $contact_name, $email, $phone, $address, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_name, email, phone, address) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $contact_name, $email, $phone, $address);
        $stmt->execute();
    }

    header("Location: suppliers.php");
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Formulaire Fournisseur</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h2><?= $id ? "Modifier Fournisseur" : "Ajouter Fournisseur" ?></h2>

<form method="post">

<div class="mb-3">
    <label>Nom</label>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control" required>
</div>

<div class="mb-3">
    <label>Nom du contact</label>
    <input type="text" name="contact_name" value="<?= htmlspecialchars($contact_name) ?>" class="form-control">
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control">
</div>

<div class="mb-3">
    <label>Téléphone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" class="form-control">
</div>

<div class="mb-3">
    <label>Adresse</label>
    <textarea name="address" class="form-control"><?= htmlspecialchars($address) ?></textarea>
</div>

<button type="submit" class="btn btn-success">Enregistrer</button>
<a href="suppliers.php" class="btn btn-secondary">Annuler</a>

</form>

</body>
</html>
