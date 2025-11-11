<?php include "db.php"; ?>

<?php
if(isset($_POST['submit'])){
    $product = $_POST['product_id'];
    $qty = $_POST['qty'];

    $conn->query("INSERT INTO purchase_orders(supplier_id, order_number, status) VALUES (1, UUID(), 'received')");
    $po_id = $conn->insert_id;

    $conn->query("INSERT INTO purchase_items(purchase_order_id, product_id, qty, unit_price)
                  VALUES ($po_id, $product, $qty, (SELECT cost_price FROM products WHERE id=$product))");

    echo "<script>alert('✅ Achat enregistré'); window.location='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter Achat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>➕ Ajouter Achat</h2>

<form method="POST">

<label>Produit</label>
<select name="product_id" required>
<?php
$p = $conn->query("SELECT id,name FROM products");
while($row = $p->fetch_assoc()):
?>
<option value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
<?php endwhile; ?>
</select>

<label>Quantité</label>
<input type="number" name="qty" required>

<button class="btn blue" type="submit" name="submit">Valider</button>
<a class="btn gray" href="dashboard.php">Retour</a>

</form>

</body>
</html>
