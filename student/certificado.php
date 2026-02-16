<?php
require "../config/db.php";
session_start();

date_default_timezone_set('America/Bogota');

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$u  = $_SESSION['user'];

if (!$id) {
    echo "Falta el ID del módulo.";
    exit;
}

/* 📘 Datos del Módulo */
$stmt = $conn->prepare("SELECT titulo FROM modules WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mod = $stmt->get_result()->fetch_assoc();

/* 📜 Datos del Certificado */
$stmt2 = $conn->prepare("SELECT fecha FROM certificates WHERE user_id = ? AND module_id = ? LIMIT 1");
$stmt2->bind_param("ii", $u['id'], $id);
$stmt2->execute();
$cert = $stmt2->get_result()->fetch_assoc();

// Fallback por si no existe el registro aún
$fecha_db = $cert ? $cert['fecha'] : date('Y-m-d');

/* 📅 Formatear Fecha */
$meses = ["01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL","05"=>"MAYO","06"=>"JUNIO",
          "07"=>"JULIO","08"=>"AGOSTO","09"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"];

$fechaObj = new DateTime($fecha_db);
$dia      = $fechaObj->format('d');
$mes_nom  = $meses[$fechaObj->format('m')];
$anio     = $fechaObj->format('Y');

$nombre_est = mb_strtoupper($u['nombre'], 'UTF-8');
$nombre_cur = mb_strtoupper($mod['titulo'], 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificado - <?= $nombre_est ?></title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Playfair+Display:wght@700&display=swap');

    body { margin: 0; padding: 40px; background: #ccc; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; }
    .no-print { margin-bottom: 20px; display: flex; gap: 10px; }
    .btn { padding: 12px 24px; background: #c1121f; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; border:none; cursor: pointer; }
    
    .certificado-box {
        position: relative;
        width: 1123px;
        height: 794px;
        background-image: url('../assets/js/img/certificado_bg.jpeg'); 
        background-size: cover;
        background-color: white;
        overflow: hidden; 
    }

    /* Parches para tapar texto estático de la imagen si es necesario */
    .patch { position: absolute; background: white; z-index: 1; }
    .patch-text { top: 50%; left: 10%; width: 80%; height: 40px; }

    .layer-text { position: absolute; width: 100%; text-align: center; z-index: 10; }

    .txt-nombre { top: 38%; font-size: 42px; font-weight: 800; color: #2c2c2c; text-transform: uppercase; }
    
    /* TEXTO MODIFICADO */
    .txt-participacion { top: 52%; font-size: 20px; color: #444; font-weight: 400; }
    
    .txt-curso { top: 57%; font-family: 'Playfair Display', serif; font-size: 34px; font-weight: 700; color: #1a1a1a; }

    .txt-fecha { top: 71%; font-size: 14px; color: #555; text-transform: uppercase; }

    @media print {
        .no-print { display: none; }
        body { padding: 0; background: none; }
        .certificado-box { box-shadow: none; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>
</head>
<body>

<div class="no-print">
    <a href="dashboard.php" class="btn" style="background:#333">⬅ Volver</a>
    <button onclick="window.print()" class="btn">🖨️ Imprimir Certificado</button>
</div>

<div class="certificado-box">
    <div class="patch patch-text"></div>

    <div class="layer-text txt-nombre"><?= $nombre_est ?></div>
    
    <div class="layer-text txt-participacion">Ha participado satisfactoriamente el módulo de capacitación:</div>
    
    <div class="layer-text txt-curso">"<?= $nombre_cur ?>"</div>

    <div class="layer-text txt-fecha">
        DADO EN SAN JOSÉ DE CÚCUTA, A LOS <strong><?= $dia ?></strong> DÍAS DEL MES DE <strong><?= $mes_nom ?></strong> DE <strong><?= $anio ?></strong>
    </div>
</div>

</body>
</html>