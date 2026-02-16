<?php
session_start();

// Limpiar sesión del admin
unset($_SESSION['admin']);
session_destroy();

// Redirigir al login de admin
header("Location: login.php");
exit;
?>
