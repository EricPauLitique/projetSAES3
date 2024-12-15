<?php
session_start();
session_destroy(); // Détruit la session

echo '<b><i style="color: red;">Vous avez été déconnectée !</i></b>';
include("../vue/connexion.html");
?>