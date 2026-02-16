<?php
require "../config/db.php";

// ✅ Validación para evitar el error de sesión duplicada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueo de seguridad
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// 1. Obtener los cursos para el desplegable
$cursos_query = $conn->query("SELECT id, titulo FROM courses WHERE estado = 1 ORDER BY titulo ASC");

// 2. Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $course_id = $_POST['course_id'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO modules (titulo, descripcion, course_id, activo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $titulo, $descripcion, $course_id, $activo);

    if ($stmt->execute()) {
        header("Location: ver_modulos.php"); 
        exit;
    } else {
        $error = "Error al crear el módulo: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Módulo</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f3f6; padding: 20px; margin: 0; }
        .card { 
            background: white; padding: 30px; border-radius: 12px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 600px; margin: 20px auto; 
            border-top: 5px solid #b91c1c; 
        }
        h2 { color: #b91c1c; margin-top: 0; font-size: 24px; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #444; font-size: 14px; }
        input[type="text"], textarea, select { 
            width: 100%; padding: 12px; margin-top: 5px; 
            border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px;
        }
        .btn-save { 
            background: #b91c1c; color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; 
            margin-top: 25px; font-size: 16px; transition: 0.3s;
        }
        .btn-save:hover { background: #991b1b; }
        
        /* Ajuste responsive */
        @media (max-width: 480px) {
            .card { padding: 20px; width: 95%; }
            h2 { font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="card">
    <h2>🧩 Crear Nuevo Módulo</h2>
    
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>Asignar a Curso:</label>
        <select name="course_id" required>
            <option value="">-- Selecciona un Curso --</option>
            <?php while($c = $cursos_query->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titulo']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Título del Módulo:</label>
        <input type="text" name="titulo" placeholder="Ej: Honestidad y Transparencia" required>

        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" placeholder="Breve explicación del contenido..."></textarea>

        <label style="display: flex; align-items: center; cursor: pointer; margin-top: 15px;">
            <input type="checkbox" name="activo" checked style="width: auto; margin-right: 10px;"> 
            <span>Módulo habilitado</span>
        </label>

        <button type="submit" class="btn-save">💾 Crear Módulo e Ir a Materiales</button>
    </form>
</div>

</body>
</html>