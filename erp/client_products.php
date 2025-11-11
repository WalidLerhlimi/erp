<?php
// client_products.php
require_once "db.php";

// Vérifier si l'utilisateur est connecté et est client
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role_name']) !== 'client') {
    header("Location: login.php");
    exit;
}

// Récupérer tous les produits
$products = fetchAll($conn, "SELECT * FROM products ORDER BY created_at DESC");

// Message pour actions
$message = "";
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4e54c8;
            padding: 15px 30px;
            color: white;
        }

        .navbar h1 { margin: 0; }
        .navbar .nav-links { display: flex; gap: 15px; }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .navbar a:hover { background-color: white; color: #4e54c8; }

        /* Container des produits */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .product-card:hover { transform: translateY(-5px); }
        .product-card h3 { margin: 10px 0 5px 0; }
        .product-card p { margin: 5px 0; color: #555; }
        .product-card .price { font-weight: bold; color: #4e54c8; }
        .product-card button {
            background-color: #4e54c8;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 15px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }
        .product-card button:hover { background-color: #3b3fc1; }

        .message { text-align: center; color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Produits</h1>
        <div class="nav-links">
            <a href="cart.php">Panier</a>
            <a href="my_orders.php">Mes commandes</a>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>

    <?php if($message != ""): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <!-- Liste des produits -->
    <div class="container">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price"><?= number_format($product['unit_price'], 2, ',', ' ') ?> €</p>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Stock : <?= intval($product['quantity']) ?></p>
                    <form method="post" action="order_product.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="order">Commander</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; width:100%;">Aucun produit disponible.</p>
        <?php endif; ?>
    </div>
</body>
</html>
