<?php
require "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

$certs = $conn->query("
    SELECT c.id AS cert_id, m.titulo, c.fecha, m.id AS module_id
    FROM certificates c
    JOIN modules m ON m.id = c.module_id
    WHERE c.user_id = ".$user['id']."
    ORDER BY c.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Certificados</title>

<style>
*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:'Segoe UI', Arial, sans-serif;
    background:#f1f3f6;
    color:#222;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

/* ================= HEADER ================= */
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

header .user{
    font-weight:600;
}

/* ================= CONTENIDO ================= */
main{
    flex:1;
}

.container{
    max-width:1200px;
    margin:auto;
    padding:50px 40px 80px;
}

h2{
    text-align:center;
    color:#b91c1c;
    font-size:30px;
    margin-bottom:35px;
}

/* BOTÓN VOLVER */
#btnDashboard{
    display:inline-block;
    margin:0 auto 45px;
    padding:14px 30px;
    border-radius:8px;
    background:#b91c1c;
    color:white;
    font-weight:600;
    text-decoration:none;
    box-shadow:0 6px 15px rgba(0,0,0,.2);
    transition:.3s;
}

#btnDashboard:hover{
    background:#991b1b;
}

/* GRID CERTIFICADOS */
.certs{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(260px,1fr));
    gap:30px;
}

/* CARD CERTIFICADO */
.cert-card{
    background:white;
    border-radius:14px;
    padding:28px 24px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    transition:.3s;
}

.cert-card:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 28px rgba(0,0,0,.15);
}

.cert-card h3{
    color:#b91c1c;
    font-size:18px;
    margin-bottom:18px;
    line-height:1.3;
}

.cert-card p{
    font-size:14px;
    margin-bottom:25px;
}

.cert-card a{
    text-decoration:none;
    background:#b91c1c;
    color:white;
    padding:12px 0;
    border-radius:6px;
    font-weight:600;
    transition:.3s;
}

.cert-card a:hover{
    background:#991b1b;
}

/* ================= FOOTER ================= */
footer{
    background:#111827;
    color:#e5e7eb;
    text-align:center;
    padding:30px 20px;
    font-size:14px;
    margin-top:60px;
}

/* ================= RESPONSIVE ================= */
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
    <div class="user">Hola, <?=htmlspecialchars($user['nombre'])?></div>
</header>

<main>
    <div class="container">

        <h2>Mis Certificados</h2>

        <div style="text-align:center;">
            <a href="dashboard.php" id="btnDashboard">⬅️ Volver al Dashboard</a>
        </div>

        <div class="certs">
            <?php if($certs->num_rows == 0): ?>
                <p style="grid-column:1/-1; text-align:center;">
                    Aún no tienes certificados disponibles.
                </p>
            <?php endif; ?>

            <?php while($c = $certs->fetch_assoc()): ?>
            <div class="cert-card">
                <h3><?=htmlspecialchars($c['titulo'])?></h3>
                <p>Fecha de expedición:<br>
                   <strong><?=date("d/m/Y", strtotime($c['fecha']))?></strong>
                </p>
                <a href="certificado.php?id=<?=$c['module_id']?>" target="_blank">
                    🖨️ Ver / Imprimir
                </a>
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
