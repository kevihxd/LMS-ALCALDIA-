<?php
require "../config/db.php";
$cid=$_GET['id'];
$uid=$_SESSION['user']['id'];

$mods=$conn->query("SELECT * FROM modules 
WHERE course_id=$cid ORDER BY orden");
?>

<h2>Módulos</h2>

<?php while($m=$mods->fetch_assoc()):
$pr=$conn->query("SELECT * FROM progress 
WHERE user_id=$uid AND module_id={$m['id']}");
$ap=$pr->fetch_assoc();
?>

<?php if($ap && $ap['aprobado']==1): ?>
✅ <?=$m['titulo']?>

<?php elseif($m['orden']==1): ?>
<a href="#">🟢 <?=$m['titulo']?></a>

<?php else: ?>
🔒 <?=$m['titulo']?>
<?php endif; ?>

<br>
<?php endwhile; ?>
