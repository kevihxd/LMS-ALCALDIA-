<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Seguridad: Solo admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Obtener ID del material
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("ID inválido");

// 3. Obtener la ruta de la imagen actual
$stmt = $conn->prepare("SELECT * FROM materials WHERE id = ? AND tipo = 'imagen'");
$stmt->bind_param("i", $id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();

if (!$material) die("Imagen no encontrada o no es editable.");

// Ajuste de rutas basándonos en tu estructura de archivos
// En BD: uploads/materiales/img_xxxx.jpg
$ruta_db = $material['contenido']; 
$ruta_fisica = __DIR__ . "/../" . $ruta_db; // Ruta para PHP (file_put_contents)
$ruta_web = "../" . $ruta_db; // Ruta para HTML (img src)

// 4. LOGICA DE GUARDADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['image_base64'];

    // Limpiar el encabezado data:image/...;base64,
    if (strpos($data, ',') !== false) {
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
    }
    $data = base64_decode($data);

    // Sobrescribir el archivo original
    if(file_put_contents($ruta_fisica, $data)){
        // Redirigir al detalle del módulo
        header("Location: modulo_detalle.php?id=" . $material['module_id']);
        exit;
    } else {
        $error = "Error al guardar la imagen en el servidor.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Imagen</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    body { background: #f1f3f6; font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px; }
    .editor-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 900px; width: 100%; }
    .img-container { height: 500px; width: 100%; background: #333; margin-bottom: 20px; }
    img { max-width: 100%; display: block; } 
    
    .toolbar { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px; }
    button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; color: white; transition: 0.2s; }
    .btn-blue { background: #2563eb; }
    .btn-green { background: #16a34a; }
    .btn-gray { background: #4b5563; }
    .btn-red { background: #dc2626; }
    button:hover { opacity: 0.9; }
    h2 { color: #b91c1c; margin-top: 0; text-align: center; }
</style>
</head>
<body>

<div class="editor-container">
    <h2>✏️ Editor de Imagen</h2>

    <div class="img-container">
        <img id="image" src="<?= htmlspecialchars($ruta_web) ?>" alt="Imagen a editar">
    </div>

    <div class="toolbar">
        <button type="button" class="btn-blue" onclick="cropper.rotate(-90)">↺ Rotar Izq</button>
        <button type="button" class="btn-blue" onclick="cropper.rotate(90)">↻ Rotar Der</button>
        <button type="button" class="btn-gray" onclick="cropper.scaleX(-1)">↔ Espejo H</button>
        <button type="button" class="btn-gray" onclick="cropper.scaleY(-1)">↕ Espejo V</button>
        <button type="button" class="btn-red" onclick="cropper.reset()">✖ Reiniciar</button>
    </div>

    <form method="POST" id="formSave">
        <input type="hidden" name="image_base64" id="image_base64">
        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="modulo_detalle.php?id=<?= $material['module_id'] ?>">
                <button type="button" class="btn-gray">Cancelar</button>
            </a>
            <button type="button" class="btn-green" onclick="saveImage()">💾 Guardar Cambios</button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    const image = document.getElementById('image');
    let cropper;

    image.onload = function() {
        cropper = new Cropper(image, {
            viewMode: 2, 
            autoCropArea: 1,
            responsive: true,
        });
    };

    function saveImage() {
        // Obtener el canvas editado
        const canvas = cropper.getCroppedCanvas({
            width: 800, // Redimensionar para optimizar
            imageSmoothingQuality: 'high',
        });

        // Convertir a base64
        const base64Url = canvas.toDataURL('image/jpeg');
        document.getElementById('image_base64').value = base64Url;
        document.getElementById('formSave').submit();
    }
</script>

</body>
</html>