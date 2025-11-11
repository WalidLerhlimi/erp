<?php
require "db.php";

// Redirection si non loggé
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// données produits pour table
$products = fetchAll($conn, "SELECT id, sku, name, quantity, unit_price, reorder_point FROM products ORDER BY name");

// données chart: stock par produit (top 10)
$chartData = fetchAll($conn, "SELECT name, quantity FROM products ORDER BY quantity DESC LIMIT 10");

// top sold approximation
$topSales = fetchAll($conn, "
    SELECT p.name, ABS(SUM(sm.change_qty)) as sold
    FROM stock_movements sm
    JOIN products p ON p.id=sm.product_id
    WHERE sm.movement_type='sale'
    GROUP BY p.id
    ORDER BY sold DESC
    LIMIT 5
");
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Dashboard ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container-fluid">
    <a class="navbar-brand" href="#">ERP - Stock</a>
    <div class="d-flex align-items-center">

        <?php if ($_SESSION['role_name'] === 'admin'): ?>
        <a class="btn btn-light btn-sm me-2" href="clients.php">👥 Clients</a>
        <a class="btn btn-light btn-sm me-2" href="users.php">👨‍💼 Employés</a>
        <a class="btn btn-warning btn-sm me-2" href="suppliers.php">🚚 Fournisseurs</a>
        <?php endif; ?>

        <span class="navbar-text me-3">Salut, <?=htmlspecialchars($_SESSION['username'])?></span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Déconnexion</a>
    </div>
</div>
</nav>

<div class="container my-4">
<div class="row g-4">

    <!-- Stock Chart -->
    <div class="col-md-8">
        <div class="card p-3">
            <h5>📦 Niveaux de stock (Top 10 produits)</h5>
            <canvas id="stockChart" height="120"></canvas>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="col-md-4">
        <div class="card p-3">
            <h5>🔥 Top ventes</h5>
            <canvas id="salesChart" height="200"></canvas>
        </div>
    </div>

    <!-- Product Table -->
    <div class="col-12">
        <div class="card p-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>Liste des produits</h5>
                <div>
                    <a class="btn btn-success btn-sm" href="product_form.php">+ Produit</a>
                    <a class="btn btn-primary btn-sm" href="purchase_form.php">+ Achat</a>
                    <a class="btn btn-warning btn-sm" href="sale_form.php">+ Vente</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-light">
                    <tr>
                        <th>Réf</th><th>Produit</th><th>Stock</th><th>Prix (MAD)</th><th>Seuil</th><th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($products as $p): ?>
                        <tr>
                            <td><?=htmlspecialchars($p['sku'])?></td>
                            <td><?=htmlspecialchars($p['name'])?></td>
                            <td><?=intval($p['quantity'])?></td>
                            <td><?=number_format($p['unit_price'],2)?></td>
                            <td><?=intval($p['reorder_point'])?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="product_form.php?id=<?=$p['id']?>">Éditer</a>
                                <a class="btn btn-sm btn-outline-danger" href="product_delete.php?id=<?=$p['id']?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Gestion employés, fournisseurs et clients -->
    <?php if ($_SESSION['role_name'] === 'admin'): ?>

    <!-- Employés -->
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>👨‍💼 Gestion des employés</h5>
                <a class="btn btn-success btn-sm" href="user_form.php">+ Nouvel employé</a>
            </div>

            <?php 
            $employees = fetchAll($conn, "
                SELECT u.id, u.username, u.email, r.name as role
                FROM users u 
                JOIN roles r ON r.id = u.role_id 
                WHERE r.name != 'client'
                ORDER BY u.username
            ");
            ?>

            <table class="table table-striped">
                <thead class="table-light">
                <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($employees as $e): ?>
                    <tr>
                        <td><?=htmlspecialchars($e['username'])?></td>
                        <td><?=htmlspecialchars($e['email'])?></td>
                        <td><?=htmlspecialchars($e['role'])?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="user_form.php?id=<?=$e['id']?>">Éditer</a>
                            <a class="btn btn-sm btn-outline-danger" href="user_delete.php?id=<?=$e['id']?>" onclick="return confirm('Supprimer cet employé ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Fournisseurs -->
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>🚚 Gestion des fournisseurs</h5>
                <a class="btn btn-success btn-sm" href="supplier_form.php">+ Nouveau fournisseur</a>
            </div>

            <?php 
            $suppliers = fetchAll($conn, "SELECT * FROM suppliers ORDER BY name");
            ?>

            <table class="table table-striped">
                <thead class="table-light">
                <tr><th>Nom</th><th>Contact</th><th>Téléphone</th><th>Email</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($suppliers as $s): ?>
                    <tr>
                        <td><?=htmlspecialchars($s['name'])?></td>
                        <td><?=htmlspecialchars($s['contact_name'])?></td>
                        <td><?=htmlspecialchars($s['phone'])?></td>
                        <td><?=htmlspecialchars($s['email'])?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="supplier_form.php?id=<?=$s['id']?>">Éditer</a>
                            <a class="btn btn-sm btn-outline-danger" href="supplier_delete.php?id=<?=$s['id']?>" onclick="return confirm('Supprimer ce fournisseur ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Clients -->
    <div class="col-12">
        <div class="card p-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>👥 Gestion des clients</h5>
                <a class="btn btn-success btn-sm" href="client_form.php">+ Nouveau client</a>
            </div>

            <?php 
            $clients = fetchAll($conn, "SELECT * FROM clients ORDER BY name");
            ?>

            <table class="table table-striped">
                <thead class="table-light">
                <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Adresse</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $c): ?>
                    <tr>
                        <td><?=htmlspecialchars($c['name'])?></td>
                        <td><?=htmlspecialchars($c['email'])?></td>
                        <td><?=htmlspecialchars($c['phone'])?></td>
                        <td><?=htmlspecialchars($c['address'])?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="client_form.php?id=<?=$c['id']?>">Éditer</a>
                            <a class="btn btn-sm btn-outline-danger" href="client_delete.php?id=<?=$c['id']?>" onclick="return confirm('Supprimer ce client ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php endif; ?>

</div>
</div>

<script>
const stockLabels = <?= json_encode(array_column($chartData,'name')) ?>;
const stockValues = <?= json_encode(array_map(fn($r)=>(int)$r['quantity'],$chartData)) ?>;

const salesLabels = <?= json_encode(array_column($topSales,'name')) ?>;
const salesValues = <?= json_encode(array_map(fn($r)=>(int)$r['sold'],$topSales)) ?>;

new Chart(document.getElementById('stockChart'), {
type: 'bar',
data: { labels: stockLabels, datasets: [{ label: 'Stock', data: stockValues }] },
options: { scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('salesChart'), {
type: 'pie',
data: { labels: salesLabels, datasets: [{ data: salesValues }] }
});
</script>

</body>
</html>
