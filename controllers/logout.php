<?php
session_start();
session_destroy(); // Détruit la session

echo "<b>Vous avez été déconnectée !</b>";
include("/controleurconnexion.php");
?>