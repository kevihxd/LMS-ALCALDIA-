<?php
require "../config/db.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueo de seguridad
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Obtener la lista de cursos
$res = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f3f6; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f1f3f6; padding-bottom: 15px; }
        h2 { color: #b91c1c; margin: 0; }

        .btn-nuevo { background: #b91c1c; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; transition: 0.3s; }
        .btn-nuevo:hover { background: #991b1b; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; color: #64748b; text-align: left; padding: 15px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #f1f3f6; color: #1e293b; font-size: 15px; }
        
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }

        .actions a { text-decoration: none; font-weight: bold; margin-right: 15px; font-size: 14px; }
        .edit { color: #2563eb; }
        .delete { color: #b91c1c; }

        .btn-back { display: inline-block; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <h2>📚 Cursos Académicos</h2>
        <a href="crear_curso.php" class="btn-nuevo">➕ Nuevo Curso</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título del Curso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><strong><?= htmlspecialchars($row['titulo']) ?></strong></td>
                <td>
                    <?php if($row['estado'] == 1): ?>
                        <span class="status-badge status-active">✅ Activo</span>
                    <?php else: ?>
                        <span class="status-badge status-inactive">❌ Inactivo</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="editar_curso.php?id=<?= $row['id'] ?>" class="edit">Editar</a>
                    <a href="eliminar_curso.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('¿Seguro que deseas eliminar este curso?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
            
            <?php if($res->num_rows == 0): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">No hay cursos registrados todavía.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn-back">⬅ Volver al Panel</a>
</div>

</body>
</html>