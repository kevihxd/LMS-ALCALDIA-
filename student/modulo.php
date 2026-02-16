<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) header("Location: dashboard.php");

// Obtener módulo y materiales
$mod = $conn->query("SELECT * FROM modules WHERE id=$id")->fetch_assoc();
$materials = $conn->query("SELECT * FROM materials WHERE module_id=$id ORDER BY orden");
$user = $_SESSION['user'];

// Funciones YouTube
function is_youtube($url) {
    return preg_match("/(youtube\.com|youtu\.be)/i", $url);
}
function youtube_embed($url) {
    if (preg_match("/youtu\.be\/([^\?\/]+)/", $url, $m)) return $m[1];
    if (preg_match("/v=([^\&]+)/", $url, $m)) return $m[1];
    return false;
}

// Separar imágenes y otros
$imagenes = [];
$otros = [];
$image_exts = ['jpg','jpeg','png','gif','webp','bmp'];

while ($m = $materials->fetch_assoc()) {
    $is_absolute = preg_match("/^https?:\/\//", $m['contenido']);
    $m['contenido'] = $is_absolute ? $m['contenido'] : "../".$m['contenido'];

    $ext = strtolower(pathinfo($m['contenido'], PATHINFO_EXTENSION));
    if ($m['tipo'] === 'imagen' || in_array($ext, $image_exts)) {
        $imagenes[] = $m;
    } else {
        $otros[] = $m;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?=htmlspecialchars($mod['titulo'])?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Segoe UI', Arial, sans-serif; }

body { background: #f1f3f6; color: #222; line-height: 1.6; }

header { background:#b91c1c; color:white; padding:18px 40px; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; }
header h2 { font-size:1.6rem; }
header div { font-size:1rem; }

.container { max-width: 1100px; margin: 30px auto; padding: 20px; }
.card { background:white; padding:35px; border-radius:16px; border-left:6px solid #b91c1c; box-shadow:0 5px 20px rgba(0,0,0,0.05); }
.card h2 { margin-bottom:15px; color:#b91c1c; }

/* GALERÍA */
.galeria {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
    margin:30px 0;
}
.galeria img {
    width:100%;
    max-width:100%;
    height:auto; /* Mantiene proporción */
    object-fit: contain;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
    cursor:pointer;
    transition: transform 0.3s ease;
}
.galeria img:hover { transform: scale(1.05); }

/* MATERIAL */
.material { background:#f9fafb; padding:20px; border-radius:12px; margin-bottom:18px; border-left:4px solid #b91c1c; }
.material p { margin-bottom:15px; }
.material a { display:inline-block; text-decoration:none; color:#b91c1c; font-weight:600; margin-bottom:10px; }
.material a:hover { text-decoration:underline; }
.material iframe { width:100%; height:auto; min-height:480px; border:none; border-radius:14px; margin-bottom:15px; }

/* BOTÓN */
.button-eval { padding:14px 26px; border:none; border-radius:10px; font-weight:600; cursor:pointer; background:#b91c1c; color:white; width:100%; margin-top:25px; font-size:1rem; transition:.3s; text-align:center; }
.button-eval:hover { background:#991b1b; }

/* LIGHTBOX */
#lightbox {
    display:none;
    position:fixed;
    z-index:9999;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.95);
    justify-content:center;
    align-items:center;
}
#lightbox img {
    max-width:95%;
    max-height:95%;
    border-radius:10px;
}
#lightbox span {
    position:absolute;
    top:20px;
    right:30px;
    font-size:2rem;
    color:white;
    cursor:pointer;
}

/* RESPONSIVE */
@media (max-width:600px) {
    header { flex-direction: column; align-items:flex-start; gap:10px; }
    .card { padding:20px; }
    .galeria img { height:auto; }
}
</style>
</head>
<body>

<header>
    <h2>Plataforma LMS</h2>
    <div>Hola, <?=htmlspecialchars($user['nombre'])?></div>
</header>

<div class="container">
<div class="card">

<h2><?=htmlspecialchars($mod['titulo'])?></h2>
<p><?=nl2br(htmlspecialchars($mod['descripcion']))?></p>

<!-- GALERÍA DE IMÁGENES -->
<?php if (!empty($imagenes)): ?>
<div class="galeria">
    <?php foreach ($imagenes as $img): ?>
        <img src="<?=htmlspecialchars($img['contenido'])?>" alt="Imagen del módulo" onclick="openLightbox(this.src)">
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- OTROS MATERIALES -->
<?php foreach ($otros as $m): ?>
<div class="material">
    <?php if ($m['tipo'] === 'texto'): ?>
        <p><?=nl2br(htmlspecialchars($m['contenido']))?></p>
    <?php elseif($m['tipo']==='video' && is_youtube($m['contenido'])): ?>
        <?php $vid_id = youtube_embed($m['contenido']); ?>
        <?php if($vid_id): ?>
            <iframe src="https://www.youtube.com/embed/<?=$vid_id?>" allowfullscreen style="height:360px;"></iframe>
        <?php else: ?>
            <a href="<?=htmlspecialchars($m['contenido'])?>" target="_blank">📎 Abrir video</a>
        <?php endif; ?>
    <?php elseif($m['tipo']==='pdf'): ?>
        <iframe src="<?=htmlspecialchars($m['contenido'])?>" style="width:100%; height:600px;"></iframe>
    <?php else: ?>
        <a href="<?=htmlspecialchars($m['contenido'])?>" target="_blank">📎 Abrir <?=htmlspecialchars($m['tipo'])?></a>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<a href="evaluacion.php?id=<?=$id?>">
    <div class="button-eval">Ir a la Evaluación →</div>
</a>

</div>
</div>

<!-- LIGHTBOX -->
<div id="lightbox" onclick="closeLightbox()">
    <span>&times;</span>
    <img src="" alt="Imagen ampliada">
</div>

<script>
function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
    lb.querySelector('img').src = src;
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}
</script>

</body>
</html>
