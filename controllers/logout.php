<?php
session_start();
$_SESSION['messageC'] = 'Vous avez été déconnecté !';
header("Location: ../vue/connexion.php");
exit;
?>