<?php
// db.php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "erp";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur connexion DB: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// helper simple pour exécuter prepared statements et fetch assoc
function fetchAll($conn, $sql, $types = null, $params = []) {
    $stmt = $conn->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
function fetchOne($conn, $sql, $types = null, $params = []) {
    $rows = fetchAll($conn, $sql, $types, $params);
    return $rows[0] ?? null;
}
?>
