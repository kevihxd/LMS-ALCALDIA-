<?php
require "../config/db.php";
session_start();

// 1. Configurar zona horaria y validar sesión
date_default_timezone_set('America/Bogota');

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Obtener datos del usuario
$u  = $_SESSION['user'];
$id_modulo = $_GET['id'] ?? null;

if (!$id_modulo) {
    header("Location: dashboard.php");
    exit;
}

// 3. Obtener el nombre del módulo (Curso)
$stmt = $conn->prepare("SELECT titulo FROM modules WHERE id = ?");
$stmt->bind_param("i", $id_modulo);
$stmt->execute();
$mod = $stmt->get_result()->fetch_assoc();

// 4. Verificar si este usuario TIENE certificado 
$stmt2 = $conn->prepare("SELECT fecha FROM certificates WHERE user_id = ? AND module_id = ? LIMIT 1");
$stmt2->bind_param("ii", $u['id'], $id_modulo);
$stmt2->execute();
$cert = $stmt2->get_result()->fetch_assoc();

if (!$cert) {
    echo "<h1 style='font-family:sans-serif; text-align:center; margin-top:50px; color:red;'>❌ No tienes certificado disponible para este curso. <br>Debes aprobar la evaluación primero.</h1>";
    exit;
}

// 
$nombre_estudiante = mb_strtoupper($u['nombre'], 'UTF-8');
$nombre_curso      = mb_strtoupper($mod['titulo'], 'UTF-8');
$cedula_estudiante = $u['cedula']; 

// Formato de fecha bonito
$meses = ["01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL","05"=>"MAYO","06"=>"JUNIO",
          "07"=>"JULIO","08"=>"AGOSTO","09"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"];
$fechaObj = new DateTime($cert['fecha']);
$dia      = $fechaObj->format('d');
$mes_nom  = $meses[$fechaObj->format('m')];
$anio     = $fechaObj->format('Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Certificado - <?= $nombre_estudiante ?></title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Playfair+Display:wght@700&display=swap');

    body {
        margin: 0;
        padding: 40px;
        background: #ccc;
        font-family: 'Montserrat', sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .no-print { margin-bottom: 20px; display: flex; gap: 10px; }
    .btn { padding: 12px 24px; background: #c1121f; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; border:none; cursor: pointer; }
    .btn-back { background: #333; }

    /* ===== CONTENEDOR DEL CERTIFICADO ===== */
    .certificado-box {
        position: relative;
        width: 1123px; 
        height: 794px;
        background-image: url('/assets/js/img/certificado_bg.jpeg'); 
        background-size: cover;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        background-color: white;
        overflow: hidden; 
    }

    
    
    .patch-name {
        position: absolute;
        top: 36%; 
        left: 5%;
        width: 90%;
        height: 90px;
        background-color: white; 
        z-index: 1;
    }
    
    
    .patch-course {
        position: absolute;
        top: 54%; 
        left: 5%;
        width: 90%;
        height: 120px;
        background-color: white;
        z-index: 1;
    }

    
    .patch-date {
        position: absolute;
        top: 70%; 
        left: 10%;
        width: 80%;
        height: 40px;
        background-color: white;
        z-index: 1;
    }


    .layer-text {
        position: absolute;
        width: 100%;
        text-align: center;
        z-index: 10; 
    }

    /* Estile  Nombre del funcionario */
    .txt-nombre {
        top: 38%; 
        font-size: 48px; 
        font-weight: 800; 
        color: #2c2c2c;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Estilo del Nombre del Curso */
    .txt-curso {
        top: 56%;
        font-family: 'Playfair Display', serif; 
        font-size: 36px;
        font-weight: 700;
        color: #1a1a1a;
        width: 80%;
        left: 10%;
        line-height: 1.2;
        text-transform: uppercase;
    }

    /* Estilo de la Fecha */
    .txt-fecha {
        top: 71%;
        font-size: 14px;
        color: #555;
        text-transform: uppercase;
    }

    @media print {
        body { padding: 0; background: none; }
        .no-print { display: none; }
        .certificado-box { box-shadow: none; margin: 0; page-break-after: always; }
        @page { size: landscape; margin: 0; }
        /* Forzar impresión de fondo e imágenes */
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>
</head>
<body>

<div class="no-print">
    <a href="dashboard.php" class="btn btn-back">⬅ Volver</a>
    <button onclick="window.print()" class="btn">🖨️ Imprimir / Guardar PDF</button>
</div>

<div class="certificado-box">
    
    <div class="patch-name"></div>
    <div class="patch-course"></div>
    <div class="patch-date"></div>

    <div class="layer-text txt-nombre"><?= $nombre_estudiante ?></div>
    
    <div class="layer-text txt-curso"><?= $nombre_curso ?></div>

    <div class="layer-text txt-fecha">
        DADO EN SAN JOSÉ DE CÚCUTA, A LOS <strong><?= $dia ?></strong> DÍAS DEL MES DE <strong><?= $mes_nom ?></strong> DE <strong><?= $anio ?></strong>
    </div>

</div>

</body>
</html>