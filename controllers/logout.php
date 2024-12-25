<?php
session_start();
session_destroy(); // Détruit la session

$_SESSION['messageC'] = '<b><i style="color: red;">Vous avez été déconnectée !</i></b>';
header("Location: ../vue/connexion.php");
?>