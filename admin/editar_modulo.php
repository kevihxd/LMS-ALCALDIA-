<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../config/db.php";

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if($id <= 0) die("ID de módulo inválido");

// Traer datos existentes
$mod = $conn->query("SELECT * FROM modules WHERE id=$id")->fetch_assoc();
if(!$mod) die("Módulo no encontrado");

// Guardar cambios
if($_POST){
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    if($titulo !== "" && $descripcion !== ""){
        $stmt = $conn->prepare("UPDATE modules SET titulo=?, descripcion=? WHERE id=?");
        $stmt->bind_param("ssi", $titulo, $descripcion, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ver_modulos.php");
        exit;
    }else{
        $msg = "Debe completar todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Módulo</title>

<style>
*{ box-sizing:border-box; }
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

header a{
    background:white;
    color:#b91c1c;
    padding:8px 16px;
    border-radius:6px;
    font-weight:600;
    text-decoration:none;
}

header a:hover{
    background:#fee2e2;
}

main{ flex:1; }

.container{
    max-width:800px;
    margin:auto;
    padding:50px 40px 80px;
}

h2{
    text-align:center;
    color:#b91c1c;
    font-size:30px;
    margin-bottom:35px;
}

.form-card{
    background:white;
    padding:35px 40px;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    border-left:6px solid #b91c1c;
}

.msg{
    background:#fee2e2;
    color:#991b1b;
    padding:12px 16px;
    border-radius:8px;
    margin-bottom:20px;
    font-weight:600;
}

label{
    font-weight:600;
    display:block;
    margin-bottom:8px;
}

input, textarea{
    width:100%;
    padding:14px 16px;
    border-radius:8px;
    border:1px solid #d1d5db;
    font-size:15px;
    margin-bottom:25px;
    font-family:'Segoe UI', Arial, sans-serif;
}

textarea{
    resize:none;
}

.actions{
    display:flex;
    gap:15px;
    justify-content:flex-end;
}

.actions button,
.actions a{
    padding:12px 28px;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    cursor:pointer;
    border:none;
    transition:.3s;
}

.actions button{
    background:#b91c1c;
    color:white;
}

.actions button:hover{
    background:#991b1b;
}

.actions a{
    background:#e5e7eb;
    color:#111827;
}

.actions a:hover{
    background:#d1d5db;
}

footer{
    background:#111827;
    color:#e5e7eb;
    text-align:center;
    padding:30px 20px;
    font-size:14px;
    margin-top:60px;
}

@media(max-width:600px){
    .container{
        padding:40px 20px 70px;
    }

    .actions{
        flex-direction:column;
    }
}
</style>
</head>

<body>

<header>
    <h1>Plataforma LMS Institucional</h1>
    <a href="ver_modulos.php">⬅ Volver</a>
</header>

<main>
    <div class="container">

        <h2>Editar módulo</h2>

        <div class="form-card">

            <?php if(isset($msg)): ?>
                <div class="msg"><?=$msg?></div>
            <?php endif; ?>

            <form method="POST">

                <label>Nombre del módulo</label>
                <input name="titulo" placeholder="Ej: Ética y Transparencia" required value="<?=htmlspecialchars($mod['titulo'])?>">

                <label>Descripción del módulo</label>
                <textarea name="descripcion" rows="5" placeholder="Descripción del contenido del módulo" required><?=htmlspecialchars($mod['descripcion'])?></textarea>

                <div class="actions">
                    <a href="ver_modulos.php">Cancelar</a>
                    <button>Actualizar módulo</button>
                </div>

            </form>

        </div>

    </div>
</main>

<footer>
    © 2026 Plataforma LMS Institucional · Todos los derechos reservados
</footer>

</body>
</html>
