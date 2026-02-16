<?php
require "../config/db.php";
session_start();

// 1. SEGURIDAD: Solo usuarios logueados
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$u = $_SESSION['user'];
$user_id = $u['id'];

// 2. VALIDACIÓN Verifica completó los 7 módulos

$check = $conn->query("SELECT COUNT(id) as total FROM certificates WHERE user_id = $user_id");
$total = $check->fetch_assoc()['total'];

if ($total < 7) {
    die("<h1 style='text-align:center; margin-top:50px;'>⚠️ Error: No has completado los 7 módulos requeridos para esta certificación.</h1>");
}

// 3. DATOS PARA EL DIPLOMA
$nombre_est = mb_strtoupper($u['nombre'], 'UTF-8');
$meses = ["01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL","05"=>"MAYO","06"=>"JUNIO",
          "07"=>"JULIO","08"=>"AGOSTO","09"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"];

$dia = date('d');
$mes_nom = $meses[date('m')];
$anio = date('Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación Final - <?= htmlspecialchars($nombre_est) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Playfair+Display:wght@700&display=swap');

        body { margin: 0; padding: 40px; background: #555; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; }
        
        /* Controles de impresión */
        .no-print { margin-bottom: 20px; display: flex; gap: 15px; }
        .btn { padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; font-size: 15px; }
        .btn-back { background: #333; color: white; }
        .btn-print { background: #b91c1c; color: white; }

        /* Contenedor del Certificado (Tamaño A4 horizontal) */
        .certificado-box {
            position: relative;
            width: 1123px;
            height: 794px;
            background: white url('../assets/js/img/certificado_global_bg.jpeg') no-repeat center;
            background-size: cover;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        /* Capas de texto con posiciones absolutas */
        .text-layer { position: absolute; width: 100%; text-align: center; left: 0; }

        .txt-nombre {
            top: 36%;
            font-size: 48px;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: uppercase;
            padding: 0 50px;
        }

        .txt-mensaje {
            top: 51%;
            font-size: 22px;
            color: #333;
            line-height: 1.6;
        }

        .txt-programa {
            top: 58%;
            font-family: 'Playfair Display', serif;
            font-size: 40px;
            font-weight: 700;
            color: #b91c1c; 
        }

        .txt-fecha {
            top: 73%;
            font-size: 15px;
            color: #555;
            letter-spacing: 1px;
        }

        /* Configuración para propia impresora */
        @media print {
            .no-print { display: none; }
            body { padding: 0; background: none; }
            .certificado-box { box-shadow: none; border: none; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="dashboard.php" class="btn btn-back">⬅ Volver al Dashboard</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Imprimir Certificación Final</button>
    </div>

    <div class="certificado-box">
        
        <div class="text-layer txt-nombre">
            <?= $nombre_est ?>
        </div>
        
        <div class="text-layer txt-mensaje">
            Ha participado y aprobado satisfactoriamente la totalidad de los módulos<br>
            de capacitación correspondientes al programa:
        </div>

        <div class="text-layer txt-programa">
            CÓDIGO DE INTEGRIDAD
        </div>

        <div class="text-layer txt-fecha">
            EXPEDIDO EN SAN JOSÉ DE CÚCUTA, A LOS <strong><?= $dia ?></strong> DÍAS DEL MES DE <strong><?= $mes_nom ?></strong> DE <strong><?= $anio ?></strong>
        </div>

    </div>

</body>
</html>