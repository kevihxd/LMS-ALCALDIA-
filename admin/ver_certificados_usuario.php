<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) die("ID de usuario no válido.");

// Obtener datos del usuario
$u_query = $conn->query("SELECT nombre, cedula FROM users WHERE id = $user_id");
$usuario = $u_query->fetch_assoc();

if (!$usuario) die("Usuario no encontrado.");


$certificados = $conn->query("
    SELECT m.titulo, c.fecha as fecha_completado, m.id as modulo_id 
    FROM modules m
    INNER JOIN certificates c ON m.id = c.module_id
    WHERE c.user_id = $user_id
    ORDER BY c.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificados de <?= htmlspecialchars($usuario['nombre']) ?></title>
    <style>
        * { box-sizing:border-box; font-family: 'Segoe UI', sans-serif; margin:0; padding:0; }
        body { background: #f1f3f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #b91c1c; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .info-user { margin-bottom: 25px; background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 5px solid #b91c1c; }
        .cert-card { display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 15px; transition: 0.3s; }
        .cert-card:hover { border-color: #b91c1c; background: #fffcfc; }
        .btn-view { background: #16a34a; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; font-size: 14px; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #b91c1c; font-weight: bold; }
        .no-results { color: #666; text-align: center; padding: 40px; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <a href="ver_usuarios.php" class="btn-back">⬅ Volver a usuarios</a>
        
        <h2>Diplomas Obtenidos</h2>
        
        <div class="info-user">
            <p style="font-size: 18px;"><strong>Estudiante:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p><strong>Cédula:</strong> <?= htmlspecialchars($usuario['cedula']) ?></p>
        </div>

        <?php if ($certificados && $certificados->num_rows > 0): ?>
            <?php while($c = $certificados->fetch_assoc()): ?>
                <div class="cert-card">
                    <div>
                        <strong style="font-size: 16px;"><?= htmlspecialchars($c['titulo']) ?></strong><br>
                        <small style="color: #666;">Fecha de emisión: <?= date("d/m/Y", strtotime($c['fecha_completado'])) ?></small>
                    </div>
                    <a href="../student/certificado.php?id=<?= $c['modulo_id'] ?>&user_id=<?= $user_id ?>" target="_blank" class="btn-view">📄 Ver Diploma</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <p>Este usuario aún no ha completado ningún módulo.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>