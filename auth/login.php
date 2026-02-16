<?php
require "../config/db.php";

if($_POST){
    $cedula = $_POST['cedula'];
    $q = $conn->query("SELECT * FROM users WHERE cedula='$cedula'");
    if($q->num_rows > 0){
        $u = $q->fetch_assoc();
        session_start();
        $_SESSION['user'] = $u;
        header("Location: LMS/student/dashboard.php");
        exit;
    }else{
        header("Location: LMS/auth/register.php?c=$cedula");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Talento Humano Contigo</title>
<style>
/* ===== BODY ===== */
body, html{
    margin:0;
    padding:0;
    height:100%;
    font-family:'Arial',sans-serif;
    overflow:hidden;
}

/* ===== FONDO ANIMADO ===== */
body{
    background: linear-gradient(-45deg, #0f172a, #1e293b, #ff2b2b, #38bdf8);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Animación de gradiente */
@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

/* ===== TARJETA LOGIN ===== */
.login-card{
    background: rgba(255,255,255,0.95);
    border-radius:20px;
    padding:50px 40px;
    max-width:400px;
    width:90%;
    box-shadow:0 15px 30px rgba(0,0,0,0.4);
    text-align:center;
    position:relative;
    z-index:1;
}

/* Sombra difusa detrás de la tarjeta */
.login-card::before{
    content:"";
    position:absolute;
    top:-20px; left:-20px; right:-20px; bottom:-20px;
    background: rgba(255,255,255,0.05);
    border-radius:25px;
    z-index:-1;
    filter: blur(20px);
}

/* ===== TÍTULO ===== */
.login-card h2{
    margin:0 0 30px 0;
    color: #1b1b1b ;
    font-size:28px;
    letter-spacing:1px;
    font-weight:bold;
}

/* ===== INPUTS ===== */
.login-card input{
    width:100%;
    padding:14px 15px;
    margin-bottom:20px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:16px;
    box-sizing:border-box;
    transition:0.3s;
}

.login-card input:focus{
    border-color:#ff2b2b;
    box-shadow:0 0 10px rgba(255,43,43,0.4);
    outline:none;
}

/* ===== BOTÓN ===== */
.login-card button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:#ff2b2b;
    color:white;
    font-weight:bold;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}

.login-card button:hover{
    background:#e02424;
    box-shadow:0 5px 15px rgba(255,43,43,0.4);
}

/* ===== TEXTO SECUNDARIO ===== */
.login-card p{
    font-size:14px;
    color:#555;
    margin-top:15px;
}

/* ===== RESPONSIVE ===== */
@media(max-width:500px){
    .login-card{
        padding:40px 25px;
    }
    .login-card h2{ font-size:24px; }
    .login-card input, .login-card button{ font-size:14px; padding:12px; }
}
</style>
</head>
<body>

<div class="login-card">
    <h2>Talento Humano Contigo</h2>
    <form method="POST">
        <input name="cedula" type="text" placeholder="Cédula" required>
        <button>Entrar</button>
    </form>
    <p>Bienvenido al portal de capacitación y gestión de talento humano.</p>
</div>

</body>
</html>