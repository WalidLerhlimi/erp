<?php
require "db.php";

if ($_SESSION['role_name'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
}

header("Location: users.php");
exit;
