<?php
require "../config/db.php";

// ✅ Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id  = intval($_GET['id'] ?? 0);
$uid = $_SESSION['user']['id'];
$msg = "";

// Procesar formulario
if ($_POST) {
    $correctas = 0;
    $total = 0;

    $q = $conn->query("SELECT * FROM questions WHERE module_id=$id");
    while ($p = $q->fetch_assoc()) {
        $total++;

        if ($p['tipo'] === "opcion_multiple") {
            $r = $_POST['p'.$p['id']] ?? 0;
            $ok = $conn->query("SELECT id FROM answers WHERE id=$r AND correcta=1");
            if ($ok->num_rows > 0) {
                $correctas++;
            }
        } else {
            $resp = trim($_POST['p'.$p['id']] ?? "");
            if ($resp !== "") {
                $correctas++;
            }
        }
    }

    $nota = ($total > 0) ? ($correctas / $total) * 100 : 0;

    if ($nota >= 50) {
        $exists = $conn->query("SELECT id FROM certificates WHERE user_id=$uid AND module_id=$id");
        if ($exists->num_rows === 0) {
            date_default_timezone_set('America/Bogota');
            $fecha = date("Y-m-d H:i:s");
            $conn->query("INSERT INTO certificates (user_id, module_id, fecha) VALUES ($uid, $id, '$fecha')");
        }
        header("Location: certificado.php?id=$id");
        exit;
    } else {
        $msg = "❌ No aprobó la evaluación. Puede intentarlo nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Evaluación</title>

<style>
*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:'Segoe UI', Arial, sans-serif;
    background:#f1f3f6;
    color:#222;
}

/* ===== HEADER ===== */
header{
    background:#b91c1c;
    color:white;
    padding:18px 40px;
    text-align:center;
    box-shadow:0 4px 12px rgba(0,0,0,.2);
}

header h1{
    margin:0;
    font-size:24px;
    letter-spacing:.5px;
}

/* ===== CONTENEDOR ===== */
.container{
    max-width:900px;
    margin:auto;
    padding:50px 25px 80px;
}

/* ===== MENSAJE ERROR ===== */
.msg{
    background:#fee2e2;
    color:#991b1b;
    padding:14px 18px;
    border-radius:10px;
    text-align:center;
    font-weight:600;
    margin-bottom:30px;
    border-left:5px solid #b91c1c;
}

/* ===== CARD PREGUNTA ===== */
.card{
    background:white;
    padding:28px 30px;
    border-radius:14px;
    margin-bottom:25px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
    border-left:6px solid #b91c1c;
}

.card p{
    font-weight:600;
    margin-bottom:18px;
    color:#b91c1c;
    font-size:18px;
}

/* ===== INPUTS ===== */
textarea{
    width:100%;
    padding:14px;
    border-radius:8px;
    border:1px solid #e5e7eb;
    font-size:15px;
    resize:vertical;
}

/* ===== OPCIONES ===== */
label{
    display:block;
    padding:14px 16px;
    background:#f9fafb;
    border-radius:8px;
    margin-bottom:12px;
    cursor:pointer;
    border:1px solid #e5e7eb;
    transition:.2s;
}

label:hover{
    background:#fef2f2;
    border-color:#b91c1c;
}

label input{
    margin-right:10px;
}

/* ===== BOTÓN ENVIAR ===== */
button{
    display:block;
    width:100%;
    margin-top:35px;
    padding:15px;
    border:none;
    border-radius:12px;
    background:#111827;
    color:white;
    font-weight:600;
    font-size:17px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#000;
}

/* ===== RESPONSIVE ===== */
@media(max-width:600px){
    header h1{ font-size:20px; }
    .card p{ font-size:16px; }
    textarea{ font-size:14px; }
    label{ font-size:14px; padding:12px; }
    button{ font-size:16px; }
}
</style>
</head>

<body>

<header>
    <h1>Evaluación del Módulo</h1>
</header>

<div class="container">

<?php if ($msg): ?>
    <div class="msg"><?=$msg?></div>
<?php endif; ?>

<form method="POST">

<?php
$q = $conn->query("SELECT * FROM questions WHERE module_id=$id");
while ($p = $q->fetch_assoc()):
?>
    <div class="card">
        <p><?=htmlspecialchars($p['pregunta'])?></p>

        <?php if ($p['tipo'] === "opcion_multiple"):
            $a = $conn->query("SELECT * FROM answers WHERE question_id=".$p['id']);
            while ($r = $a->fetch_assoc()):
        ?>
            <label>
                <input type="radio" name="p<?=$p['id']?>" value="<?=$r['id']?>" required>
                <?=htmlspecialchars($r['texto'])?>
            </label>
        <?php endwhile; else: ?>
            <textarea name="p<?=$p['id']?>" rows="4" placeholder="Escriba su respuesta..." required></textarea>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<button>✅ Enviar Evaluación</button>

</form>

</div>

</body>
</html>
