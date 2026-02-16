<?php
require "../config/db.php";

// Validación de sesión inteligente para evitar el Notice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueo de seguridad
if(!isset($_SESSION['admin'])) { 
    header("Location: login.php"); 
    exit; 
}

// Consulta de cursos
$cursos = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f3f6; padding: 20px; margin: 0; }
        .card { 
            background: white; padding: 30px; border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 900px; margin: auto; 
        }
        h2 { color: #b91c1c; margin-top: 0; }
        
        /* Botones */
        .btn { padding: 10px 18px; border-radius: 6px; text-decoration: none; color: white; font-weight: bold; display: inline-block; transition: 0.3s; font-size: 14px; }
        .btn-add { background: #16a34a; margin-bottom: 20px; }
        .btn-add:hover { background: #15803d; }
        .btn-edit { background: #2563eb; }
        .btn-edit:hover { background: #1d4ed8; }
        .btn-back { background: #64748b; margin-top: 20px; }

        /* Tabla Responsive */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 500px; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #b91c1c; color: white; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
        tr:hover { background: #f8fafc; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        @media (max-width: 600px) {
            .card { padding: 15px; }
            th, td { padding: 10px; font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>📚 Cursos</h2>
        <a href="crear_curso.php" class="btn btn-add">➕ Nuevo Curso</a>
        
        <div class="table-container">
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
                    <?php while($c = $cursos->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?= $c['id'] ?></strong></td>
                        <td><?= htmlspecialchars($c['titulo']) ?></td>
                        <td>
                            <?php if($c['estado'] == 1): ?>
                                <span class="badge badge-active">✅ Activo</span>
                            <?php else: ?>
                                <span class="badge badge-inactive">❌ Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_curso.php?id=<?= $c['id'] ?>" class="btn btn-edit">Editar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-back">⬅ Volver al Panel</a>
    </div>
</body>
</html>