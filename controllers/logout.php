<?php
session_start();
session_destroy();
session_start(); // Redémarre la session pour pouvoir définir un message
$_SESSION['message'] = 'Vous avez été déconnecté !';
echo json_encode(['status' => 'success']);
exit;
?>