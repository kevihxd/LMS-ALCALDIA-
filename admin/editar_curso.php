<?php
require "../config/db.php";

// Validación de sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueo de seguridad: solo admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// 1. Obtener datos actuales del curso
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) {
    die("Curso no encontrado.");
}

// 2. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $estado = intval($_POST['estado']);

    $update = $conn->prepare("UPDATE courses SET titulo = ?, estado = ? WHERE id = ?");
    $update->bind_param("sii", $titulo, $estado, $id);

    if ($update->execute()) {
        header("Location: ver_cursos.php");
        exit;
    } else {
        $error = "Error al actualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f3f6; padding: 20px; margin: 0; }
        .card { 
            background: white; padding: 30px; border-radius: 12px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 500px; margin: 40px auto; 
            border-top: 5px solid #b91c1c; 
        }
        h2 { color: #b91c1c; margin-top: 0; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #444; }
        input[type="text"], select { 
            width: 100%; padding: 12px; margin-top: 5px; 
            border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; 
        }
        .btn-update { 
            background: #b91c1c; color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; 
            margin-top: 25px; font-size: 16px; 
        }
        .btn-update:hover { background: #991b1b; }
        .link-back { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>📝 Editar Curso</h2>
    
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>Título del Curso:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($curso['titulo']) ?>" required>

        <label>Estado del Curso:</label>
        <select name="estado">
            <option value="1" <?= $curso['estado'] == 1 ? 'selected' : '' ?>>✅ Activo (Visible para alumnos)</option>
            <option value="0" <?= $curso['estado'] == 0 ? 'selected' : '' ?>>❌ Inactivo (Borrador)</option>
        </select>

        <button type="submit" class="btn-update">💾 Guardar Cambios</button>
    </form>
    
    <a href="ver_cursos.php" class="link-back">Cancelar y volver</a>
</div>

</body>
</html>