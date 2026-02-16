<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// 🔒 Forzar sesión exclusiva de admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Info del admin
$user = $_SESSION['admin'];

// Obtener id del módulo
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) die("ID de módulo inválido");

// Traer módulo
$mod = $conn->query("SELECT * FROM modules WHERE id=$id")->fetch_assoc();
if (!$mod) die("Módulo no encontrado");

// Traer materiales y preguntas
$materials = $conn->query("SELECT * FROM materials WHERE module_id=$id ORDER BY orden");
$questions = $conn->query("SELECT * FROM questions WHERE module_id=$id");

// Funciones YouTube
function is_youtube($url) {
    return preg_match("/(youtube\.com|youtu\.be)/i", $url);
}
function youtube_embed($url) {
    if (preg_match("/youtu\.be\/([^\?\/]+)/", $url, $m)) return $m[1];
    if (preg_match("/v=([^\&]+)/", $url, $m)) return $m[1];
    return false;
}

// Separar imágenes y otros materiales
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=htmlspecialchars($mod['titulo'])?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial, sans-serif; }
body { background:#f1f3f6; color:#222; line-height:1.6; display:flex; flex-direction:column; min-height:100vh; }
header { background:#b91c1c; color:white; padding:18px 40px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; box-shadow:0 4px 12px rgba(0,0,0,.2); }
header h1 { font-size:22px; margin:0; }
header .user { font-weight:600; }
.container { max-width:1200px; margin:auto; padding:50px 40px 80px; }
h2 { text-align:center; color:#b91c1c; font-size:30px; margin-bottom:20px; }
.descripcion { background:white; padding:25px; border-radius:14px; box-shadow:0 6px 18px rgba(0,0,0,.08); margin-bottom:35px; line-height:1.6; }

.botones { display:flex; gap:15px; justify-content:center; margin-bottom:45px; flex-wrap:wrap; }
.botones a { text-decoration:none; background:#b91c1c; color:white; padding:14px 28px; border-radius:8px; font-weight:600; transition:.3s; }
.botones a:hover { background:#991b1b; }
.botones a.edit { background:#2563eb; }
.botones a.edit:hover { background:#1e40af; }

.galeria { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:20px; margin-bottom:30px; }
.galeria img { width:100%; max-width:100%; height:auto; object-fit:contain; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,.25); cursor:pointer; transition:transform 0.3s ease; }
.galeria img:hover { transform: scale(1.05); }

.section { background:white; padding:30px; border-radius:16px; margin-bottom:40px; box-shadow:0 8px 20px rgba(0,0,0,.08); border-left:6px solid #b91c1c; }
.section h3 { color:#b91c1c; margin-bottom:25px; font-size:22px; }
.material { background:#f9fafb; padding:20px; border-radius:12px; margin-bottom:20px; border-left:4px solid #b91c1c; }
.material strong { display:block; margin-bottom:10px; color:#111827; }
.material p { margin:0 0 15px; }
.material img { width:100%; max-height:420px; object-fit:contain; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,.25); background:white; padding:10px; cursor:pointer; }
.material a { color:#b91c1c; font-weight:600; text-decoration:none; }
.material a:hover { text-decoration:underline; }
.material iframe { width:100%; height:400px; border:none; border-radius:14px; margin-bottom:15px; }

#lightbox { display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); justify-content:center; align-items:center; }
#lightbox img { max-width:95%; max-height:95%; border-radius:10px; }
#lightbox span { position:absolute; top:20px; right:30px; font-size:2rem; color:white; cursor:pointer; }

@media(max-width:600px) {
    header { flex-direction:column; gap:10px; text-align:center; }
    .container { padding:40px 20px 70px; }
    h2 { font-size:24px; }
    .botones { flex-direction:column; }
}
</style>
</head>
<body>

<header>
    <h1>Plataforma LMS</h1>
    <div class="user">Hola, <?=htmlspecialchars($user['usuario'])?></div>
    <a href="ver_modulos.php" style="display:inline-block;margin-top:10px;padding:8px 16px;background-color:#4CAF50;color:white;text-decoration:none;border-radius:5px;font-weight:bold;">Regresar</a>
</header>

<main>
<div class="container">

<h2><?=htmlspecialchars($mod['titulo'])?></h2>

<div class="descripcion">
    <?=nl2br(htmlspecialchars($mod['descripcion']))?>
</div>

<div class="botones">
    <a href="crear_material.php?id=<?=$id?>">➕ Agregar Material</a>
    <a href="crear_pregunta.php?id=<?=$id?>">❓ Crear Pregunta</a>
    <a href="editar_modulo.php?id=<?=$id?>" class="edit">✏️ Editar Módulo</a>
</div>

<?php if(!empty($imagenes)): ?>
<div class="galeria">
    <?php foreach($imagenes as $img): ?>
        <img src="<?=htmlspecialchars($img['contenido'])?>" alt="Imagen del módulo" onclick="openLightbox(this.src)">
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if(!empty($otros)): ?>
    <?php foreach($otros as $m): ?>
        <div class="material">
            <strong><?=strtoupper($m['tipo'])?></strong>
            <?php if($m['tipo']==='texto'): ?>
                <p><?=nl2br(htmlspecialchars($m['contenido']))?></p>
            <?php elseif($m['tipo']==='video' && is_youtube($m['contenido'])): ?>
                <?php $vid_id = youtube_embed($m['contenido']); ?>
                <?php if($vid_id): ?>
                    <iframe src="https://www.youtube.com/embed/<?=$vid_id?>" allowfullscreen></iframe>
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
<?php else: ?>
    <p>No hay materiales agregados.</p>
<?php endif; ?>

<div class="section">
<h3>🧠 Preguntas</h3>
<?php if($questions->num_rows > 0): ?>
    <?php while($q = $questions->fetch_assoc()): ?>
        <div class="material"><?=htmlspecialchars($q['pregunta'])?></div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay preguntas agregadas.</p>
<?php endif; ?>
</div>

</div>
</main>

<footer>
© <?=date('Y')?> Plataforma LMS — Todos los derechos reservados
</footer>

<div id="lightbox" onclick="closeLightbox()">
    <span>&times;</span>
    <img src="" alt="Imagen ampliada">
</div>

<script>
function openLightbox(src){
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
    lb.querySelector('img').src = src;
}
function closeLightbox(){
    document.getElementById('lightbox').style.display = 'none';
}
</script>

</body>
</html>
