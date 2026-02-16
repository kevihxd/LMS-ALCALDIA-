<?php
require "../config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Administrador</title>

<style>
/* =========================
   CONFIG GENERAL
========================= */
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

/* =========================
   HEADER
========================= */
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
    letter-spacing:.5px;
}

.header-right{
    display:flex;
    align-items:center;
    gap:20px;
}

.header-right .user{
    font-weight:600;
}

.header-right a{
    background:white;
    color:#b91c1c;
    padding:8px 16px;
    border-radius:6px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}

.header-right a:hover{
    background:#fee2e2;
}

/* =========================
   CONTENIDO
========================= */
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

/* =========================
   GRID ADMIN
========================= */
.cursos{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:30px;
}

.curso-card{
    background:white;
    border-radius:14px;
    padding:26px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    transition:.3s;
}

.curso-card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 28px rgba(0,0,0,.15);
}

.curso-card b{
    font-size:18px;
    margin-bottom:25px;
    line-height:1.3;
}

.curso-card a{
    text-align:center;
    background:#b91c1c;
    color:white;
    padding:12px 0;
    border-radius:6px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}

.curso-card a:hover{
    background:#991b1b;
}

/* CARD ESPECIAL PARA CURSOS */
.curso-card.gold{
    border-left-color: #f59e0b;
}
.curso-card.gold a{
    background: #f59e0b;
}
.curso-card.gold a:hover{
    background: #d97706;
}

/* CARD PELIGRO */
.curso-card.logout{
    border-left-color:#111827;
}

.curso-card.logout a{
    background:#111827;
}

.curso-card.logout a:hover{
    background:#000;
}

/* =========================
   FOOTER
========================= */
footer{
    background:#111827;
    color:#e5e7eb;
    text-align:center;
    padding:30px 20px;
    font-size:14px;
    margin-top:auto;
}

/* =========================
   RESPONSIVE
========================= */
@media(max-width:600px){
    header{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

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
    <h1>Plataforma LMS Institucional</h1>
    <div class="header-right">
        <div class="user">Admin: <?=htmlspecialchars($admin['usuario'] ?? 'Administrador')?></div>
        <a href="logout2.php">Cerrar sesión</a>
    </div>
</header>

<main>
    <div class="container">

        <h2>Panel Administrador</h2>

        <div class="cursos">

            <div class="curso-card gold">
                <b>➕ Crear nuevo curso</b>
                <a href="crear_curso.php">Ir</a>
            </div>

            <div class="curso-card gold">
                <b>📚 Gestionar cursos</b>
                <a href="ver_cursos.php">Ir</a>
            </div>

            <div class="curso-card">
                <b>➕ Crear nuevo módulo</b>
                <a href="crear_modulo.php">Ir</a>
            </div>

            <div class="curso-card">
                <b>🧩 Gestionar módulos</b>
                <a href="ver_modulos.php">Ir</a>
            </div>

            <div class="curso-card">
                <b>👥 Gestión de usuarios</b>
                <a href="ver_usuarios.php">Ir</a>
            </div>

            <div class="curso-card logout">
                <b>🛡️ Cerrar sesión administrador</b>
                <a href="logout2.php">Salir</a>
            </div>

        </div>

    </div>
</main>

<footer>
    © 2026 Plataforma LMS Institucional · Todos los derechos reservados
</footer>

</body>
</html>