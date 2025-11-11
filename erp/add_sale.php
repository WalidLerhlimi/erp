<?php include "db.php"; ?>

<?php
if(isset($_POST['submit'])){
    $product = $_POST['product_id'];
    $qty = $_POST['qty'];

    // Création commande
    $conn->query("INSERT INTO sale_orders(client_id, order_number, status) VALUES (1, UUID(), 'confirmed')");
    $sale_id = $conn->insert_id;

    // Ajouter item
    $conn->query("INSERT INTO sale_items(sale_order_id, product_id, qty, unit_price)
                  VALUES ($sale_id, $product, $qty, (SELECT unit_price FROM products WHERE id=$product))");

    echo "<script>alert('✅ Vente enregistrée'); window.location='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter Vente</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>➕ Ajouter Vente</h2>

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

<button class="btn" type="submit" name="submit">Valider</button>
<a class="btn gray" href="dashboard.php">Retour</a>

</form>

</body>
</html>
