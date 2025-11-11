<?php
require_once "db.php";

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role_name']) !== 'client') {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$message = "";

// Supprimer un produit
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if (isset($cart[$remove_id])) {
        unset($cart[$remove_id]);
        $_SESSION['cart'] = $cart;
    }
}

// Passer la commande et enregistrer en DB
if (isset($_POST['checkout']) && count($cart) > 0) {
    $total_order = 0;
    foreach ($cart as $item) {
        $total_order += $item['unit_price'] * $item['quantity'];
    }

    // Insérer la commande
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->bind_param("id", $_SESSION['user_id'], $total_order);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insérer les articles
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $product_id => $item) {
        $stmt_item->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['unit_price']);
        $stmt_item->execute();
    }
    $stmt_item->close();

    // Vider le panier
    $_SESSION['cart'] = [];
    $message = "Commande enregistrée avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
        .navbar { display:flex; justify-content:space-between; align-items:center; background:#4e54c8; padding:15px 30px; color:white; }
        .navbar h1 { margin:0; }
        .navbar .nav-links { display:flex; gap:15px; }
        .navbar a { color:white; text-decoration:none; padding:8px 15px; border:1px solid white; border-radius:5px; transition:0.3s; }
        .navbar a:hover { background:white; color:#4e54c8; }

        .container { max-width:900px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; margin-bottom:30px; }
        table th, table td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
        table th { background:#4e54c8; color:white; }
        .btn { padding:8px 12px; background:#4e54c8; color:white; border:none; border-radius:5px; cursor:pointer; text-decoration:none; }
        .btn:hover { background:#3b3fc1; }
        .message { text-align:center; color:green; margin-bottom:15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Panier</h1>
        <div class="nav-links">
            <a href="client_products.php">Produits</a>
            <a href="my_orders.php">Mes commandes</a>
        </div>
    </div>

    <div class="container">
        <?php if($message != "") echo "<div class='message'>{$message}</div>"; ?>

        <?php if(count($cart) > 0): ?>
            <form method="post" action="">
                <table>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    <?php
                        $total_cart = 0;
                        foreach($cart as $id => $item):
                            $total = $item['unit_price'] * $item['quantity'];
                            $total_cart += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['unit_price'], 2, ',', ' ') ?> €</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($total, 2, ',', ' ') ?> €</td>
                        <td>
                            <a class="btn" href="cart.php?remove=<?= $id ?>">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="3">Total du panier</th>
                        <th colspan="2"><?= number_format($total_cart, 2, ',', ' ') ?> €</th>
                    </tr>
                </table>
                <button type="submit" name="checkout" class="btn">Passer la commande</button>
            </form>
        <?php else: ?>
            <p style="text-align:center;">Votre panier est vide.</p>
        <?php endif; ?>
    </div>
</body>
</html>
