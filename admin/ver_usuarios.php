<?php
require "../config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['admin'])){
    header("Location: /lms/login");
    exit;
}

$admin = $_SESSION['admin'];

// ==========================
// Lógica de Filtros y Búsqueda
// ==========================
$buscar = isset($_GET['q']) ? $_GET['q'] : ''; 
$filtro_secretaria = isset($_GET['s']) ? $_GET['s'] : '';

$condiciones = [];

// Filtro por texto (Nombre o Cédula)
if ($buscar != '') {
    $condiciones[] = "(nombre LIKE '%$buscar%' OR cedula LIKE '%$buscar%')";
}

// Filtro por Secretaría
if ($filtro_secretaria != '') {
    $condiciones[] = "secretaria = '$filtro_secretaria'";
}

// Construir el WHERE dinámicamente
$where_clause = "";
if (count($condiciones) > 0) {
    $where_clause = " WHERE " . implode(' AND ', $condiciones);
}

// Obtener usuarios filtrados
$u = $conn->query("SELECT * FROM users $where_clause ORDER BY id DESC");

// Obtener lista de secretarías únicas para el desplegable
$secretarias_query = $conn->query("SELECT DISTINCT secretaria FROM users WHERE secretaria != '' ORDER BY secretaria ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>

<style>
*{ box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', Arial, sans-serif; }
body{ background:#f1f3f6; color:#222; min-height:100vh; display:flex; flex-direction:column; }

header{ background:#b91c1c; color:white; padding:18px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.2); }
header h1{ font-size:22px; }
header a{ background:white; color:#b91c1c; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:600; }

/* ===== ESTILOS DEL BUSCADOR ACTUALIZADOS ===== */
.search-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.search-container input, .search-container select {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
}
.search-container input { flex: 2; min-width: 200px; }
.search-container select { flex: 1; min-width: 150px; cursor: pointer; }

.btn-search {
    background: #b91c1c;
    color: white;
    border: none;
    padding: 0 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
.btn-clear {
    background: #6b7280;
    color: white;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 8px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

main{ flex:1; padding:40px 20px; }
.container{ max-width:1200px; margin:auto; }
h2{ text-align:center; color:#b91c1c; font-size:28px; margin-bottom:30px; }

.users{ display:grid; grid-template-columns:repeat(auto-fit, minmax(320px,1fr)); gap:25px; }
.user-card{ background:white; padding:25px; border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,.08); border-left:6px solid #b91c1c; }
.user-card h3{ color:#b91c1c; margin-bottom:12px; font-size:18px; }
.user-card p{ font-size:14px; margin:6px 0; color:#111827; }
.user-card span{ font-weight:600; color:#991b1b; }

.btn-certificados {
    display: inline-block;
    margin-top: 15px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    width: 100%;
    text-align: center;
}

footer{ background:#111827; color:#e5e7eb; text-align:center; padding:30px 20px; font-size:14px; margin-top:40px; }
</style>
</head>

<body>

<header>
    <h1>Panel Administrador</h1>
    <a href="dashboard.php">⬅ Volver al panel</a>
</header>

<main>
<div class="container">
    <h2>👥 Usuarios Registrados</h2>

    <form class="search-container" method="GET">
        <input type="text" name="q" placeholder="Nombre o cédula..." value="<?= htmlspecialchars($buscar) ?>">
        
        <select name="s">
            <option value="">Todas las Secretarías</option>
            <?php while($sec = $secretarias_query->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($sec['secretaria']) ?>" <?= ($filtro_secretaria == $sec['secretaria']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sec['secretaria']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="btn-search">🔍 Filtrar</button>
        
        <?php if($buscar != '' || $filtro_secretaria != ''): ?>
            <a href="ver_usuarios.php" class="btn-clear">Limpiar</a>
        <?php endif; ?>
    </form>

    <div class="users">
    <?php if($u && $u->num_rows > 0): ?>
        <?php while($x = $u->fetch_assoc()): ?>
            <div class="user-card">
                <h3><?=htmlspecialchars($x['nombre'])?></h3>
                <p><span>Cédula:</span> <?=htmlspecialchars($x['cedula'])?></p>
                <p><span>Secretaría:</span> <?=htmlspecialchars($x['secretaria'])?></p>
                <p><span>Correo:</span> <?=htmlspecialchars($x['correo'])?></p>
                <p><span>Tipo:</span> <?=htmlspecialchars($x['tipo_funcionario'])?></p>
                
                <a href="ver_certificados_usuario.php?id=<?= $x['id'] ?>" class="btn-certificados">
                    🎓 Ver Certificados
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; grid-column: 1/-1; padding: 40px; color: #666;">No se encontraron usuarios con estos filtros.</p>
    <?php endif; ?>
    </div>
</div>
</main>

<footer>
    © 2026 Plataforma LMS Institucional · Todos los derechos reservados
</footer>

</body>
</html>