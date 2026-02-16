<?php
session_start(); // iniciar sesión

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login amigable
header("Location: /lms/login");
exit;
