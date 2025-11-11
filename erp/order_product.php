<?php
// order_product.php
require_once "db.php";

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role_name']) !== 'client') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['order'])) {
    $product_id = intval($_POST['product_id']);
    $product = fetchOne($conn, "SELECT * FROM products WHERE id = ?", "i", [$product_id]);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'unit_price' => $product['unit_price'],
                'quantity' => 1
            ];
        }

        $message = "Produit ajouté au panier.";
        header("Location: client_products.php?message=" . urlencode($message));
        exit;
    } else {
        $message = "Produit introuvable.";
        header("Location: client_products.php?message=" . urlencode($message));
        exit;
    }
} else {
    header("Location: client_products.php");
    exit;
}
