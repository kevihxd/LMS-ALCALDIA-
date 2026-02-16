<?php
require "../config/db.php";

// ================= SESIÓN =================

// ================= ID MÓDULO =================
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// ================= GUARDAR MATERIAL =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = $_POST['tipo'] ?? '';
    $contenido = '';

    // ========= IMAGEN =========
    if ($tipo === 'imagen' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

        $permitidas = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $permitidas)) {
            die("❌ Formato de imagen no permitido");
        }

        if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
            die("❌ La imagen no puede superar 3MB");
        }

        $dirFisico = __DIR__ . "/../uploads/materiales/";
        if (!is_dir($dirFisico)) mkdir($dirFisico, 0777, true);

        $nombre = uniqid("img_") . "." . $ext;
        $rutaFisica = $dirFisico . $nombre;
        $rutaBD = "uploads/materiales/" . $nombre;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisica)) {
            die("❌ Error al subir la imagen");
        }

        $contenido = $rutaBD;

    } 
    // ========= PDF =========
    elseif ($tipo === 'pdf' && isset($_FILES['pdf']) && $_FILES['pdf']['error'] === 0) {

        $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') die("❌ Solo se permiten archivos PDF");
        if ($_FILES['pdf']['size'] > 10 * 1024 * 1024) die("❌ El PDF no puede superar 10MB");

        $dirFisico = __DIR__ . "/../uploads/materiales/";
        if (!is_dir($dirFisico)) mkdir($dirFisico, 0777, true);

        $nombre = uniqid("pdf_") . "." . $ext;
        $rutaFisica = $dirFisico . $nombre;
        $rutaBD = "uploads/materiales/" . $nombre;

        if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $rutaFisica)) {
            die("❌ Error al subir el PDF");
        }

        $contenido = $rutaBD;

    } 
    // ========= TEXTO / LINK / VIDEO =========
    else {
        $contenido = trim($_POST['contenido'] ?? '');
        if ($contenido === '') die("❌ El contenido no puede estar vacío");
        $contenido = $conn->real_escape_string($contenido);
    }

    $tipo = $conn->real_escape_string($tipo);

    $conn->query("INSERT INTO materials (module_id, tipo, contenido) VALUES ($id, '$tipo', '$contenido')");

    header("Location: modulo_detalle.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar Material</title>
<style>
*{ box-sizing:border-box; }
body{ margin:0; font-family:'Segoe UI', Arial, sans-serif; background:#f1f3f6; }
header{ background:#b91c1c; color:white; padding:18px 40px; display:flex; align-items:center; }
.container{ max-width:700px; margin:60px auto; padding:0 20px; }
.card{ background:white; padding:35px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,.12); border-left:6px solid #b91c1c; }
.card h2{ margin-top:0; color:#b91c1c; text-align:center; }
label{ font-weight:600; margin-top:18px; display:block; }
input, select{ width:100%; padding:14px; margin-top:6px; border-radius:8px; border:1px solid #e5e7eb; }
.actions{ display:flex; gap:15px; margin-top:30px; }
button{ flex:1; padding:14px; background:#111827; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer; }
button:hover{ background:#000; }
a{ flex:1; text-align:center; padding:14px; background:#b91c1c; color:white; text-decoration:none; border-radius:10px; }
a:hover{ background:#991b1b; }
</style>
<script>
function toggleInput(){
    const tipo = document.getElementById("tipo").value;
    document.getElementById("campoTexto").style.display  = (tipo==="imagen" || tipo==="pdf") ? "none":"block";
    document.getElementById("campoImagen").style.display = (tipo==="imagen") ? "block":"none";
    document.getElementById("campoPDF").style.display = (tipo==="pdf") ? "block":"none";
}
</script>
</head>
<body>

<header>
    <h1>LMS Portal</h1>
</header>

<div class="container">
<div class="card">

<h2>Agregar Material</h2>

<form method="POST" enctype="multipart/form-data">

    <label>Tipo de material</label>
    <select name="tipo" id="tipo" onchange="toggleInput()" required>
        <option value="">Seleccione…</option>
        <option value="pdf">PDF</option>
        <option value="video">Video</option>
        <option value="link">Link</option>
        <option value="texto">Texto</option>
        <option value="imagen">Imagen</option>
    </select>

    <div id="campoTexto">
        <label>Contenido (URL o texto)</label>
        <input type="text" name="contenido" placeholder="https://... o texto">
    </div>

    <div id="campoImagen" style="display:none">
        <label>Subir imagen</label>
        <input type="file" name="imagen" accept="image/*">
    </div>

    <div id="campoPDF" style="display:none">
        <label>Subir PDF</label>
        <input type="file" name="pdf" accept="application/pdf">
    </div>

    <div class="actions">
        <a href="modulo_detalle.php?id=<?=$id?>">⬅ Volver</a>
        <button type="submit">💾 Guardar</button>
    </div>

</form>

</div>
</div>

</body>
</html>
