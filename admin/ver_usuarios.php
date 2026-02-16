<?php
require "../config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// ==========================
// Verificar sesión de admin
// ==========================
if(!isset($_SESSION['admin'])){
    header("Location: /lms/login"); // URL amigable
    exit;
}

$admin = $_SESSION['admin'];

// ==========================
// Traer usuarios
// ==========================
$u = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>

<style>
*{ box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', Arial, sans-serif; }

body{
    background:#f1f3f6;
    color:#222;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* ===== HEADER ===== */
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
    font-size:22px;
}

header a{
    background:white;
    color:#b91c1c;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    font-weight:600;
}

header a:hover{
    background:#fee2e2;
}

/* ===== CONTENIDO ===== */
main{
    flex:1;
    padding:40px 20px;
}

.container{
    max-width:1200px;
    margin:auto;
}

/* ===== TITULO ===== */
h2{
    text-align:center;
    color:#b91c1c;
    font-size:28px;
    margin-bottom:30px;
}

/* ===== GRID USUARIOS ===== */
.users{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(320px,1fr));
    gap:25px;
}

/* ===== CARD USUARIO ===== */
.user-card{
    background:white;
    padding:25px;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
    transition:0.3s;
}

.user-card:hover{
    transform:translateY(-4px);
    box-shadow:0 15px 30px rgba(0,0,0,.15);
}

/* ===== NOMBRE ===== */
.user-card h3{
    color:#b91c1c;
    margin-bottom:12px;
    font-size:18px;
}

/* ===== DATOS ===== */
.user-card p{
    font-size:14px;
    margin:6px 0;
    color:#111827;
}

.user-card span{
    font-weight:600;
    color:#991b1b;
}

/* ===== FOOTER ===== */
footer{
    background:#111827;
    color:#e5e7eb;
    text-align:center;
    padding:30px 20px;
    font-size:14px;
    margin-top:40px;
}

/* ===== RESPONSIVE ===== */
@media(max-width:600px){
    h2{ font-size:24px; }
    .user-card h3{ font-size:16px; }
    .user-card p{ font-size:13px; }
}
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

    <div class="users">
    <?php while($x = $u->fetch_assoc()): ?>
        <div class="user-card">
            <h3><?=htmlspecialchars($x['nombre'])?></h3>
            <p><span>ID:</span> <?=htmlspecialchars($x['id'])?></p>
            <p><span>Cédula:</span> <?=htmlspecialchars($x['cedula'])?></p>
            <p><span>Secretaría:</span> <?=htmlspecialchars($x['secretaria'])?></p>
            <p><span>Sexo:</span> <?=htmlspecialchars($x['sexo'])?></p>
            <p><span>Correo:</span> <?=htmlspecialchars($x['correo'])?></p>
            <p><span>Teléfono:</span> <?=htmlspecialchars($x['telefono'])?></p>
            <p><span>Tipo funcionario:</span> <?=htmlspecialchars($x['tipo_funcionario'])?></p>
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
