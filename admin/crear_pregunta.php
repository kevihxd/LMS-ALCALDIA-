<?php
require "../config/db.php";
$id = $_GET['id'];
$msg = "";

// Guardar la pregunta
if($_POST){
    $pregunta = trim($_POST['pregunta']);
    $tipo = $_POST['tipo'];

    if($pregunta == ""){
        $msg = "Debe escribir la pregunta";
    } else {
        $conn->query("INSERT INTO questions(module_id,pregunta,tipo) VALUES($id,'$pregunta','$tipo')");
        $question_id = $conn->insert_id;

        if($tipo == "opcion_multiple" && isset($_POST['opciones'])){
            foreach($_POST['opciones'] as $index => $op){
                $correcta = (isset($_POST['correcta']) && $_POST['correcta']==$index) ? 1 : 0;
                $op_text = trim($op);
                if($op_text != ""){
                    $conn->query("INSERT INTO answers(question_id,texto,correcta) VALUES($question_id,'$op_text',$correcta)");
                }
            }
        }

        header("Location: modulo_detalle.php?id=$id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Pregunta</title>
<style>
/* =========================
   BODY Y HEADER
========================= */
body{
    margin:0;
    font-family:'Segoe UI', Arial, sans-serif;
    background:#f1f3f6;
    color:#222;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

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
}

header .header-right{
    display:flex;
    align-items:center;
    gap:20px;
}

header .header-right .user{
    font-weight:600;
}

header .header-right a{
    background:white;
    color:#b91c1c;
    padding:8px 16px;
    border-radius:6px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}

header .header-right a:hover{
    background:#fee2e2;
}

/* =========================
   CONTENIDO
========================= */
main{ flex:1; }

.container{
    max-width:600px;
    margin:auto;
    padding:50px 20px 80px;
}

h2{
    text-align:center;
    color:#b91c1c;
    font-size:28px;
    margin-bottom:30px;
}

/* ===== FORM ===== */
form{
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

input, textarea, select, button{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:none;
    border-radius:8px;
    font-size:14px;
}

input, textarea, select{
    background:#f1f3f6;
    color:#222;
}

button{
    background:#b91c1c;
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#991b1b;
}

/* ===== OPCIONES MÚLTIPLES ===== */
.opciones div{
    display:flex;
    margin-bottom:10px;
    align-items:center;
}

.opciones input[type="text"]{
    flex:1;
    margin-right:10px;
    padding:10px;
    font-size:14px;
    border-radius:6px;
    border:1px solid #ccc;
    background:#f1f3f6;
    color:#222;
}

.opciones input[type="radio"]{
    width:auto;
    height:auto;
    transform:scale(1.2);
}

/* MENSAJE DE ERROR */
.error-msg{
    color:#b91c1c;
    font-weight:600;
    margin-bottom:15px;
}
</style>

<script>
function toggleOpciones(){
    var tipo = document.getElementById("tipo").value;
    var cont = document.getElementById("opciones_container");
    cont.style.display = (tipo == "opcion_multiple") ? "block" : "none";
}
</script>
</head>

<body>
<header>
    <h1>Panel Admin</h1>
    <div class="header-right">
        <span class="user">Pedroislol</span>
        <a href="logout.php">Salir</a>
    </div>
</header>

<main>
<div class="container">
<h2>Crear Pregunta</h2>

<?php if($msg != "") echo "<p class='error-msg'>$msg</p>"; ?>

<form method="POST">
    <input name="pregunta" placeholder="Pregunta" required>

    <select name="tipo" id="tipo" onchange="toggleOpciones()" required>
        <option value="abierta">Abierta</option>
        <option value="opcion_multiple">Opción múltiple</option>
    </select>

    <div id="opciones_container" style="display:none;" class="opciones">
        <p>Opciones (marca la correcta):</p>
        <div><input type="text" name="opciones[]" placeholder="Opción 1"><input type="radio" name="correcta" value="0"></div>
        <div><input type="text" name="opciones[]" placeholder="Opción 2"><input type="radio" name="correcta" value="1"></div>
        <div><input type="text" name="opciones[]" placeholder="Opción 3"><input type="radio" name="correcta" value="2"></div>
        <div><input type="text" name="opciones[]" placeholder="Opción 4"><input type="radio" name="correcta" value="3"></div>
    </div>

    <button>Guardar</button>
</form>
</div>
</main>
</body>
</html>
