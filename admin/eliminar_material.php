<?php
require "../config/db.php";
session_start();

// Solo admin puede borrar
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 1. Busca el archivo para saber su ruta
    $stmt = $conn->prepare("SELECT * FROM materials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $material = $stmt->get_result()->fetch_assoc();

    if ($material) {
        // 2. Constructor de la ruta física y borra el archivo del servidor
        $ruta_archivo = __DIR__ . "/../" . $material['contenido'];
        
        if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo); //borra la foto de la carpeta uploads
        }

        // 3. Borrar el registro de la db
        $conn->query("DELETE FROM materials WHERE id = $id");
        

        header("Location: modulo_detalle.php?id=" . $material['module_id']);
        exit;
    }
}

header("Location: ver_modulos.php");
?>