<?php
require "db.php";
require "auth_check.php";

$id = $_GET['id'] ?? null;
$product = null;
if($id) {
    $product = fetchOne($conn, "SELECT * FROM products WHERE id=?", "i", [$id]);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $unit = $_POST['unit_price'];
    $cost = $_POST['cost_price'];
    $qty = $_POST['quantity'];
    $reorder = $_POST['reorder_point'];

    if(isset($_POST['id']) && $_POST['id']) {
        $stmt = $conn->prepare("UPDATE products SET sku=?, name=?, description=?, unit_price=?, cost_price=?, quantity=?, reorder_point=? WHERE id=?");
        $stmt->bind_param("sssddiii", $sku, $name, $desc, $unit, $cost, $qty, $reorder, $_POST['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO products (sku, name, description, unit_price, cost_price, quantity, reorder_point) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssddii", $sku, $name, $desc, $unit, $cost, $qty, $reorder);
        $stmt->execute();
    }
    header("Location: dashboard.php");
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Produit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h4><?= $product ? "Éditer produit" : "Nouveau produit" ?></h4>
  <form method="post" class="card p-3">
    <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">
    <div class="mb-3">
      <label class="form-label">SKU</label>
      <input class="form-control" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nom</label>
      <input class="form-control" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea class="form-control" name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
    </div>
    <div class="row">
      <div class="col">
        <label class="form-label">Prix vente</label>
        <input class="form-control" name="unit_price" type="number" step="0.01" value="<?= $product['unit_price'] ?? 0 ?>">
      </div>
      <div class="col">
        <label class="form-label">Prix coût</label>
        <input class="form-control" name="cost_price" type="number" step="0.01" value="<?= $product['cost_price'] ?? 0 ?>">
      </div>
    </div>
    <div class="row mt-3">
      <div class="col">
        <label class="form-label">Quantité</label>
        <input class="form-control" name="quantity" type="number" value="<?= $product['quantity'] ?? 0 ?>">
      </div>
      <div class="col">
        <label class="form-label">Seuil alerte</label>
        <input class="form-control" name="reorder_point" type="number" value="<?= $product['reorder_point'] ?? 0 ?>">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-success">Enregistrer</button>
      <a class="btn btn-secondary" href="dashboard.php">Annuler</a>
    </div>
  </form>
</div>
</body>
</html>
