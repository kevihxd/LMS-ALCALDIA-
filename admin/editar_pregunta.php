<?php
require "../config/db.php";
session_start();

// 1. Validar Admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Obtener ID de la pregunta
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("ID inválido");

// 3. Obtener datos actuales
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pregunta = $stmt->get_result()->fetch_assoc();

if (!$pregunta) die("Pregunta no encontrada");

// 4. Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo   = $_POST['pregunta'];
    $op_a     = $_POST['opcion_a'];
    $op_b     = $_POST['opcion_b'];
    $op_c     = $_POST['opcion_c'];
    $op_d     = $_POST['opcion_d'];
    $correcta = $_POST['correcta'];

    $update = $conn->prepare("UPDATE questions SET pregunta=?, opcion_a=?, opcion_b=?, opcion_c=?, opcion_d=?, correcta=? WHERE id=?");
    $update->bind_param("ssssssi", $titulo, $op_a, $op_b, $op_c, $op_d, $correcta, $id);
    
    if ($update->execute()) {
        header("Location: modulo_detalle.php?id=" . $pregunta['module_id']);
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
<title>Editar Pregunta</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; display: flex; justify-content: center; padding: 40px; }
    .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
    h2 { color: #2563eb; margin-top: 0; text-align: center; }
    label { display: block; margin-top: 15px; font-weight: bold; color: #444; }
    input[type="text"], select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
    .btn-group { margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end; }
    button { padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
    .save { background: #2563eb; color: white; }
    .cancel { background: #9ca3af; color: white; text-decoration: none; display: inline-block; text-align: center; }
</style>
</head>
<body>

<div class="card">
    <h2>✏️ Editar Pregunta</h2>
    
    <form method="POST">
        <label>Enunciado de la Pregunta:</label>
        <input type="text" name="pregunta" value="<?=htmlspecialchars($pregunta['pregunta'])?>" required>

        <label>Opción A:</label>
        <input type="text" name="opcion_a" value="<?=htmlspecialchars($pregunta['opcion_a'])?>" required>

        <label>Opción B:</label>
        <input type="text" name="opcion_b" value="<?=htmlspecialchars($pregunta['opcion_b'])?>" required>

        <label>Opción C:</label>
        <input type="text" name="opcion_c" value="<?=htmlspecialchars($pregunta['opcion_c'])?>" required>

        <label>Opción D:</label>
        <input type="text" name="opcion_d" value="<?=htmlspecialchars($pregunta['opcion_d'])?>" required>

        <label>Respuesta Correcta:</label>
        <select name="correcta" required>
            <option value="a" <?= $pregunta['correcta'] == 'a' ? 'selected' : '' ?>>Opción A</option>
            <option value="b" <?= $pregunta['correcta'] == 'b' ? 'selected' : '' ?>>Opción B</option>
            <option value="c" <?= $pregunta['correcta'] == 'c' ? 'selected' : '' ?>>Opción C</option>
            <option value="d" <?= $pregunta['correcta'] == 'd' ? 'selected' : '' ?>>Opción D</option>
        </select>

        <div class="btn-group">
            <a href="modulo_detalle.php?id=<?= $pregunta['module_id'] ?>" class="cancel">Cancelar</a>
            <button type="submit" class="save">💾 Guardar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>