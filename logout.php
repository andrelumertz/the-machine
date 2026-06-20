<?php
session_start();
session_destroy(); // Invalida o crachá do usuário
header("Location: login.php");
exit();
?>
