<?php
// home.php
require_once "db.php";

// Récupérer tous les produits depuis la table `products`
$products = fetchAll($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
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

        .navbar h1 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 5px;
            transition: 0.3s;
        }

        .navbar a:hover {
            background-color: white;
            color: #4e54c8;
        }

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

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card h3 {
            margin: 10px 0 5px 0;
        }

        .product-card p {
            margin: 5px 0;
            color: #555;
        }

        .product-card .price {
            font-weight: bold;
            color: #4e54c8;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Mon E-commerce</h1>
        <a href="login.php">Connexion</a>
    </div>

    <!-- Liste des produits -->
    <div class="container">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price"><?= number_format($product['unit_price'], 2, ',', ' ') ?> €</p>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Stock : <?= intval($product['quantity']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; width:100%;">Aucun produit disponible.</p>
        <?php endif; ?>
    </div>
</body>
</html>
