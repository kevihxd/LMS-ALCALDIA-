<?php
require "../config/db.php";
session_start();

// 1. SEGURIDAD: Solo usuarios logueados
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$course_id = intval($_GET['course_id'] ?? 0);

// 2. VALIDACIÓN: Verificar que el usuario ha aprobado todos los módulos del curso
$stmt_val = $conn->prepare("
    SELECT 
        (SELECT COUNT(id) FROM modules WHERE course_id = ? AND activo = 1) as total,
        (SELECT COUNT(c.id) FROM certificates c INNER JOIN modules m ON c.module_id = m.id WHERE c.user_id = ? AND m.course_id = ?) as aprobados
");
$stmt_val->bind_param("iii", $course_id, $user_id, $course_id);
$stmt_val->execute();
$res = $stmt_val->get_result()->fetch_assoc();

if ($res['total'] == 0 || $res['aprobados'] < $res['total']) {
    die("<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>⚠️ Error: Aún no has completado todos los módulos de este curso.</h1>");
}

// 3. DATOS PARA EL DIPLOMA
$u = $_SESSION['user'];
$nombre_est = mb_strtoupper($u['nombre'], 'UTF-8');

// Obtener el nombre del curso
$stmt_c = $conn->prepare("SELECT titulo FROM courses WHERE id = ?");
$stmt_c->bind_param("i", $course_id);
$stmt_c->execute();
$curso_info = $stmt_c->get_result()->fetch_assoc();
$nombre_curso = mb_strtoupper($curso_info['titulo'], 'UTF-8');

// Fecha actual formateada
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
    <title>Certificado de Curso - <?= htmlspecialchars($nombre_est) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Playfair+Display:wght@700&display=swap');

        body { margin: 0; padding: 40px; background: #333; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; }
        
        /* Controles */
        .no-print { margin-bottom: 20px; display: flex; gap: 15px; }
        .btn { padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; font-size: 15px; }
        .btn-back { background: #eee; color: #333; }
        .btn-print { background: #f59e0b; color: white; } 

        /*  Diploma */
        .certificado-box {
            position: relative;
            width: 1123px;
            height: 794px;
            background: white url('../assets/js/img/certificado_curso_bg.jpeg') no-repeat center;
            background-size: cover;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            overflow: hidden;
            text-align: center;
        }

        .capa-texto { position: absolute; width: 100%; left: 0; }

        .txt-estudiante {
            top: 36%;
            font-size: 50px;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: uppercase;
        }

        .txt-mensaje {
            top: 51%;
            font-size: 22px;
            color: #444;
            line-height: 1.5;
        }

        .txt-curso {
            top: 58%;
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: #b91c1c;
        }

        .txt-fecha {
            top: 75%;
            font-size: 16px;
            color: #666;
            letter-spacing: 1px;
        }

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
        <a href="ver_modulos.php?course_id=<?= $course_id ?>" class="btn btn-back">⬅ Volver a Módulos</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Imprimir Certificado de Curso</button>
    </div>

    <div class="certificado-box">
        
        <div class="capa-texto txt-estudiante">
            <?= $nombre_est ?>
        </div>
        
        <div class="capa-texto txt-mensaje">
            Ha aprobado satisfactoriamente la totalidad de los módulos<br>
            que integran el curso académico:
        </div>

        <div class="capa-texto txt-curso">
            "<?= $nombre_curso ?>"
        </div>

        <div class="capa-texto txt-fecha">
            DADO EN SAN JOSÉ DE CÚCUTA, A LOS <strong><?= $dia ?></strong> DÍAS DEL MES DE <strong><?= $mes_nom ?></strong> DE <strong><?= $anio ?></strong>
        </div>

    </div>

</body>
</html>