<?php
// my_orders.php
require_once "db.php";

// Vérifier si l'utilisateur est connecté et est client
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role_name']) !== 'client') {
    header("Location: login.php");
    exit;
}

// Récupérer toutes les commandes de l'utilisateur
$orders = fetchAll($conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", "i", [$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes commandes</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4; }
        .navbar { display:flex; justify-content:space-between; align-items:center; background:#4e54c8; padding:15px 30px; color:white; }
        .navbar h1 { margin:0; }
        .navbar a { color:white; text-decoration:none; padding:8px 15px; border:1px solid white; border-radius:5px; transition:0.3s; }
        .navbar a:hover { background:white; color:#4e54c8; }
        .container { max-width:900px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
        h2 { margin-top:0; color:#4e54c8; }
        table { width:100%; border-collapse:collapse; margin-bottom:30px; }
        table th, table td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
        table th { background:#4e54c8; color:white; }
        .empty { text-align:center; margin-top:20px; color:#555; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Mes commandes</h1>
        <a href="client_products.php">Retour aux produits</a>
    </div>

    <div class="container">
        <?php if(count($orders) > 0): ?>
            <?php foreach($orders as $order): ?>
                <h2>Commande #<?= $order['id'] ?> - <?= htmlspecialchars($order['status']) ?> - <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></h2>
                <?php
                    $items = fetchAll($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", "i", [$order['id']]);
                    $total = 0;
                ?>
                <table>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                    <?php foreach($items as $item): 
                        $item_total = $item['unit_price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format($item['unit_price'], 2, ',', ' ') ?> €</td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item_total, 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="3">Total commande</th>
                        <th><?= number_format($total, 2, ',', ' ') ?> €</th>
                    </tr>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">Vous n'avez aucune commande pour le moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
