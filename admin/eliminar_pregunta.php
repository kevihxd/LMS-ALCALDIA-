<?php
require "../config/db.php";
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Obtener module_id antes de borrar para saber a dónde volver
    $stmt = $conn->prepare("SELECT module_id FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res) {
        $conn->query("DELETE FROM questions WHERE id = $id");
        header("Location: modulo_detalle.php?id=" . $res['module_id']);
        exit;
    }
}

header("Location: ver_modulos.php"); // Fallback
?>