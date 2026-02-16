<?php
require "../config/db.php";
session_start();

if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }

$user_id = $_SESSION['user']['id'];
$course_id = intval($_GET['course_id'] ?? 0);

// 1. Obtener info del curso seleccionado
$stmt = $conn->prepare("SELECT titulo FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) die("Curso no encontrado.");

// 2. Obtener los módulos vinculados al curso
$stmt_mods = $conn->prepare("SELECT * FROM modules WHERE course_id = ? AND activo = 1 ORDER BY id ASC");
$stmt_mods->bind_param("i", $course_id);
$stmt_mods->execute();
$modulos = $stmt_mods->get_result();
$total_modulos = $modulos->num_rows; 

// 3. Obtener los IDs de los módulos que el usuario YA aprobó
$aprobados = [];
$res_certs = $conn->query("SELECT module_id FROM certificates WHERE user_id = $user_id");
while($row = $res_certs->fetch_assoc()){
    $aprobados[] = $row['module_id'];
}

// 4. LÓGICA DE CERTIFICADO DE CURSO:
// Filtro array de aprobados solo los que pertenecen a este curso
$aprobados_en_este_curso = 0;
$modulos_clon = $conn->query("SELECT id FROM modules WHERE course_id = $course_id AND activo = 1");
while($m_check = $modulos_clon->fetch_assoc()){
    if(in_array($m_check['id'], $aprobados)) {
        $aprobados_en_este_curso++;
    }
}


$curso_completo = ($total_modulos > 0 && $aprobados_en_este_curso >= $total_modulos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulos de <?= htmlspecialchars($curso['titulo']) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f3f6; margin: 0; }
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .btn-regresar { text-decoration: none; color: #b91c1c; font-weight: bold; margin-bottom: 25px; display: inline-block; }
        
        /* Estilos del Aviso de Curso Completado */
        .alerta-exito {
            background: #fff9db; border: 2px solid #f59e0b; padding: 25px; 
            border-radius: 12px; text-align: center; margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.1);
        }
        .btn-certificado-global {
            background: #f59e0b; color: white; padding: 12px 25px; 
            border-radius: 8px; text-decoration: none; font-weight: bold; 
            display: inline-block; margin-top: 10px; transition: 0.3s;
        }
        .btn-certificado-global:hover { background: #d97706; transform: scale(1.03); }

        .modulo-card { 
            background: white; padding: 25px; border-radius: 12px; margin-bottom: 15px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 6px solid #b91c1c;
            transition: 0.2s;
        }

        .modulo-aprobado { border-left: 6px solid #16a34a; }
        .info-modulo { flex-grow: 1; margin-right: 20px; }
        .check-icon { font-size: 24px; color: #16a34a; margin-right: 15px; }

        .btn-estudiar { background: #b91c1c; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; min-width: 100px; text-align: center; }
        .btn-completado { background: #16a34a; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn-regresar">⬅ Volver a Cursos</a>
    
    <h1 style="color: #b91c1c; margin-bottom: 5px;"><?= htmlspecialchars($curso['titulo']) ?></h1>
    <p style="color: #555; margin-bottom: 30px;">Progreso: <?= $aprobados_en_este_curso ?> de <?= $total_modulos ?> módulos finalizados.</p>

    <?php if($curso_completo): ?>
        <div class="alerta-exito">
            <h2 style="color: #92400e; margin: 0 0 10px 0;">¡FELICITACIONES! 🏅</h2>
            <p style="color: #92400e; font-size: 16px;">Has completado satisfactoriamente todos los módulos de este curso.</p>
            <a href="certificado_curso.php?course_id=<?= $course_id ?>" class="btn-certificado-global">
                📜 DESCARGAR CERTIFICADO DEL CURSO
            </a>
        </div>
    <?php endif; ?>

    <?php if($modulos->num_rows > 0): ?>
        <?php while($m = $modulos->fetch_assoc()): ?>
            <?php 
                $esta_aprobado = in_array($m['id'], $aprobados); 
            ?>
            
            <div class="modulo-card <?= $esta_aprobado ? 'modulo-aprobado' : '' ?>">
                
                <?php if($esta_aprobado): ?>
                    <div class="check-icon" title="Módulo Completado">✅</div>
                <?php endif; ?>

                <div class="info-modulo">
                    <strong style="font-size: 18px; color: #111827;">
                        <?= htmlspecialchars($m['titulo']) ?>
                    </strong>
                    <p style="margin: 5px 0 0; color: #6b7280; font-size: 14px;">
                        <?= htmlspecialchars($m['descripcion']) ?>
                    </p>
                </div>

                <?php if($esta_aprobado): ?>
                    <a href="modulo.php?id=<?= $m['id'] ?>" class="btn-completado">Repasar</a>
                <?php else: ?>
                    <a href="modulo.php?id=<?= $m['id'] ?>" class="btn-estudiar">Comenzar</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: #666; padding: 40px;">No hay módulos disponibles para este curso todavía.</p>
    <?php endif; ?>
</div>

</body>
</html>