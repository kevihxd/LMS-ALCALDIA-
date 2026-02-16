<?php
require __DIR__ . "/../config/db.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

// Consulta Cursos Activos ---
$cursos = $conn->query("SELECT * FROM courses WHERE estado = 1 ORDER BY titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Cursos | LMS</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f1f3f6; margin: 0; }
    header { background: #b91c1c; color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
    .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
    h2 { color: #111827; margin-bottom: 30px; }
    
    /* Grid de Cursos */
    .grid-cursos { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
    
    .curso-card { 
        background: white; border-radius: 15px; padding: 30px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 6px solid #b91c1c;
        display: flex; flex-direction: column; justify-content: space-between;
        transition: 0.3s;
    }
    .curso-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    
    .curso-card h3 { margin: 0 0 15px; color: #b91c1c; font-size: 22px; }
    .btn-entrar { 
        background: #b91c1c; color: white; text-align: center; 
        padding: 12px; border-radius: 8px; text-decoration: none; 
        font-weight: bold; margin-top: 20px;
    }
    .btn-logout { color: white; text-decoration: none; font-weight: bold; border: 1px solid white; padding: 8px 15px; border-radius: 6px; }
</style>
</head>
<body>

<header>
    <h1>Capacitación Municipal</h1>
    <div style="display:flex; align-items:center; gap:20px;">
        <span>Hola, <strong><?= htmlspecialchars($user['nombre']) ?></strong></span>
        <a href="../auth/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</header>

<div class="container">
    <h2>📚 Mis Cursos Disponibles</h2>

    <div class="grid-cursos">
        <?php if($cursos->num_rows > 0): ?>
            <?php while($c = $cursos->fetch_assoc()): ?>
                <div class="curso-card">
                    <h3><?= htmlspecialchars($c['titulo']) ?></h3>
                    <p style="color: #666;">Haz clic abajo para ver los módulos y lecciones de este curso.</p>
                    
                    <a href="ver_modulos.php?course_id=<?= $c['id'] ?>" class="btn-entrar">Explorar Módulos</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aún no hay cursos asignados para ti.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>