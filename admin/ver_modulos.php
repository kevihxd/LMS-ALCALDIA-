<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// ===== Cambiar estado =====
if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $mod = $conn->query("SELECT activo FROM modules WHERE id=$id")->fetch_assoc();
    $nuevo = ($mod['activo'] == 1) ? 0 : 1;
    $conn->query("UPDATE modules SET activo=$nuevo WHERE id=$id");
    header("Location: ver_modulos.php");
    exit;
}

// ===== Eliminar módulo =====
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM modules WHERE id=$id");
    header("Location: ver_modulos.php");
    exit;
}

$mods = $conn->query("SELECT * FROM modules ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ver Módulos</title>

<style>
*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:'Segoe UI', Arial, sans-serif;
    background:#f1f3f6;
    color:#222;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

header{
    background:#b91c1c;
    color:white;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 12px rgba(0,0,0,.2);
}

header h1{
    margin:0;
    font-size:22px;
}

header a{
    background:white;
    color:#b91c1c;
    padding:8px 16px;
    border-radius:6px;
    font-weight:600;
    text-decoration:none;
}

header a:hover{
    background:#fee2e2;
}

main{ flex:1; }

.container{
    max-width:1200px;
    margin:auto;
    padding:50px 40px 80px;
}

h2{
    text-align:center;
    color:#b91c1c;
    font-size:30px;
    margin-bottom:40px;
}

.modules{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:30px;
}

.module-card{
    background:white;
    border-radius:16px;
    padding:26px 24px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    transition:.3s;
}

.module-card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 28px rgba(0,0,0,.15);
}

.module-card b{
    font-size:18px;
    margin-bottom:20px;
    line-height:1.3;
}

.status{
    position:absolute;
    top:18px;
    right:18px;
    font-size:14px;
    font-weight:600;
}

.activo{ color:#16a34a; }
.inactivo{ color:#dc2626; }

.actions{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.actions a{
    text-align:center;
    padding:12px 0;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}

.actions a.primary{
    background:#b91c1c;
    color:white;
}

.actions a.primary:hover{
    background:#991b1b;
}

.actions a.secondary{
    background:#e5e7eb;
    color:#111827;
}

.actions a.secondary:hover{
    background:#d1d5db;
}

.actions a.edit{
    background:#2563eb;
    color:white;
}

.actions a.edit:hover{
    background:#1d4ed8;
}

.actions a.delete{
    background:#dc2626;
    color:white;
}

.actions a.delete:hover{
    background:#b91c1c;
}

footer{
    background:#111827;
    color:#e5e7eb;
    text-align:center;
    padding:30px 20px;
    font-size:14px;
    margin-top:60px;
}

@media(max-width:600px){
    .container{
        padding:40px 20px 70px;
    }

    h2{
        font-size:24px;
    }
}
</style>
</head>

<body>

<header>
    <h1>Panel Administrador</h1>
    <a href="dashboard.php">⬅ Volver</a>
</header>

<main>
    <div class="container">

        <h2>📚 Módulos</h2>

        <div class="modules">
            <?php while($m = $mods->fetch_assoc()): ?>
            <div class="module-card">

                <b><?=htmlspecialchars($m['titulo'])?></b>

                <div class="status <?=$m['activo'] ? 'activo':'inactivo'?>">
                    <?=$m['activo'] ? '🟢 Activo' : '🔴 Inactivo'?>
                </div>

                <div class="actions">
                    <a href="modulo_detalle.php?id=<?=$m['id']?>" class="primary">
                        Administrar
                    </a>

                    <a href="editar_modulo.php?id=<?=$m['id']?>" class="edit">
                        Editar
                    </a>

                    <a href="ver_modulos.php?delete=<?=$m['id']?>" class="delete" onclick="return confirm('¿Seguro que quieres eliminar este módulo?');">
                        Eliminar
                    </a>

                    <a href="ver_modulos.php?toggle=<?=$m['id']?>" class="secondary">
                        <?=$m['activo'] ? 'Desactivar' : 'Activar'?>
                    </a>
                </div>

            </div>
            <?php endwhile; ?>
        </div>

    </div>
</main>

<footer>
    © 2026 Plataforma LMS Institucional · Todos los derechos reservados
</footer>

</body>
</html>
