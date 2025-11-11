<?php
require "db.php";
require "auth_check.php";
if(!isAdmin()) die("Accès refusé");
$id = intval($_GET['id'] ?? 0);
if($id){
  $stmt = $conn->prepare("DELETE FROM clients WHERE id=?");
  $stmt->bind_param("i",$id); $stmt->execute();
}
header("Location: clients.php"); exit;
