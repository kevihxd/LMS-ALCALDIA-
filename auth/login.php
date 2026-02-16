<?php
require "../config/db.php";

if($_POST){
    $cedula = $_POST['cedula'];
    $password = $_POST['password']; 

    // Consulta para validar existencia de usuario
    $q = $conn->query("SELECT * FROM users WHERE cedula='$cedula'");
    
    if($q->num_rows > 0){
        $u = $q->fetch_assoc();
        
        // Verificación de hash de contraseña
        if(password_verify($password, $u['password'])){
            session_start();
            $_SESSION['user'] = $u;
            
            // Login exitoso -> Dashboard
            header("Location: ../student/dashboard.php");
            exit;
        } else {
            // Error en contraseña
            $error = "La contraseña ingresada es incorrecta.";
        }
    } else {
        // Usuario no existe -> Redirección a registro con cedula por GET
        header("Location: register.php?c=$cedula");
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
/* Estilos generales del contenedor y animaciones */
body, html{ margin:0; padding:0; height:100%; font-family:'Arial',sans-serif; overflow:hidden; }

body{
    background: linear-gradient(-45deg, #0f172a, #1e293b, #ff2b2b, #38bdf8);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    display:flex; justify-content:center; align-items:center;
}

@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.login-card{
    background: rgba(255,255,255,0.95);
    border-radius:20px; padding:50px 40px;
    max-width:400px; width:90%;
    box-shadow:0 15px 30px rgba(0,0,0,0.4);
    text-align:center; position:relative; z-index:1;
}

.login-card h2{ margin:0 0 30px 0; color: #1b1b1b; font-size:28px; font-weight:bold; }

.login-card input{
    width:100%; padding:14px 15px; margin-bottom:15px;
    border-radius:10px; border:1px solid #ccc; font-size:16px;
    box-sizing:border-box; transition:0.3s;
}

.login-card input:focus{ border-color:#ff2b2b; outline:none; }

.login-card button{
    width:100%; padding:14px; border:none; border-radius:10px;
    background:#ff2b2b; color:white; font-weight:bold; font-size:16px;
    cursor:pointer; transition:0.3s; margin-top: 10px;
}

.login-card button:hover{ background:#e02424; box-shadow:0 5px 15px rgba(255,43,43,0.4); }

.error-msg { color: #b91c1c; font-size: 14px; margin-bottom: 15px; font-weight: bold; }

.login-card p{ font-size:14px; color:#555; margin-top:20px; }

/* Enlace de registro */
.reg-link { margin-top: 15px; display: block; font-size: 14px; color: #555; text-decoration: none; }
.reg-link b { color: #ff2b2b; }
</style>
</head>
<body>

<div class="login-card">
    <h2>Talento Humano Contigo</h2>
    
    <?php if(isset($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input name="cedula" type="text" placeholder="Cédula" required>
        <input name="password" type="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
    
    <a href="register.php" class="reg-link">¿No tienes cuenta? <b>Regístrate aquí</b></a>
    
    <p>Bienvenido al portal de capacitación y gestión de talento humano.</p>
</div>

</body>
</html>