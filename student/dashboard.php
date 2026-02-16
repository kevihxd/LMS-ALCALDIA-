<?php
require __DIR__ . "/../config/db.php";

// ✅ Iniciar sesión solo si no hay una activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ✅ Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: /lms/login");
    exit;
}

$user = $_SESSION['user'];

// Traer módulos
$mods = $conn->query("SELECT * FROM modules");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Plataforma LMS Institucional</title>

<style>
/* =========================
   CONFIG GENERAL
========================= */
*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family: 'Segoe UI', Arial, sans-serif;
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
main{
    flex:1;
}

.container{
    max-width:1200px;
    margin:auto;
    padding:50px 40px 80px;
}

/* BOTÓN CERTIFICADOS */
#btnCertificados{
    display:inline-block;
    background:#b91c1c;
    color:white;
    padding:14px 28px;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    margin-bottom:40px;
    box-shadow:0 6px 15px rgba(0,0,0,.2);
    transition:.3s;
}

#btnCertificados:hover{
    background:#991b1b;
}

/* =========================
   GRID CURSOS
========================= */
.cursos{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:30px;
}

.curso-card{
    background:white;
    border-radius:14px;
    padding:24px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    transition:.3s;
}

.curso-card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 28px rgba(0,0,0,.15);
}

.curso-card b{
    font-size:18px;
    margin-bottom:20px;
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

/* BLOQUEADO */
.candado{
    position:absolute;
    top:16px;
    right:18px;
    font-size:22px;
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
    margin-top:60px;
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
}
</style>
</head>

<body>

<header>
    <h1>Plataforma LMS Institucional</h1>
    <div class="header-right">
        <div class="user">Hola, <?=htmlspecialchars($user['nombre'])?></div>
        <!-- 🔑 Enlace de cerrar sesión corregido -->
        <a href="/auth/logout.php">Cerrar sesión</a>
    </div>
</header>

<main>
    <div class="container">

        <a href="mis_certificados.php" id="btnCertificados">🎓 Mis Certificados</a>

        <div class="cursos">
            <?php while($m = $mods->fetch_assoc()): ?>
            <div class="curso-card">
                <b><?=htmlspecialchars($m['titulo'])?></b>

                <?php if(isset($m['activo']) && $m['activo'] == 0): ?>
                    <div class="candado">🔒</div>
                    <a href="#" onclick="alert('Este módulo aún no está habilitado');return false;">Bloqueado</a>
                <?php else: ?>
                    <a href="modulo.php?id=<?=$m['id']?>">Entrar al módulo</a>
                <?php endif; ?>
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
