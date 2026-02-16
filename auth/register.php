<?php
require "../config/db.php";

$cedula_previa = isset($_GET['c']) ? $_GET['c'] : '';

if($_POST){
    $cedula = $_POST['cedula'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $secretaria = $_POST['secretaria'];
    $sexo = $_POST['sexo'];
    $telefono = $_POST['telefono'];
    $tipo_funcionario = $_POST['tipo_funcionario'];

    $sql = "INSERT INTO users (cedula, password, nombre, correo, secretaria, sexo, telefono, tipo_funcionario) 
            VALUES ('$cedula', '$password', '$nombre', '$correo', '$secretaria', '$sexo', '$telefono', '$tipo_funcionario')";
    
    if($conn->query($sql)){
        header("Location: login.php?registered=1");
        exit;
    }
}

$secretarias = [
    "Despacho del Alcalde", "Secretaría de Gobierno", "Secretaría de Hacienda",
    "Secretaría de Educación", "Secretaría de Salud", "Secretaría de Infraestructura",
    "Secretaría de Tránsito y Transporte", "Secretaría de Planeación",
    "Secretaría de Desarrollo Social", "Secretaría de Cultura y Turismo",
    "Secretaría de Equidad de Género", "Secretaría de Seguridad Ciudadana",
    "Secretaría de Gestión del Riesgo", "Oficina TIC", "Oficina Jurídica"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Alcaldía de Cúcuta</title>
    <style>
        body, html{ margin:0; padding:0; min-height:100%; font-family:'Segoe UI', sans-serif; }
        body{
            background: linear-gradient(-45deg, #0f172a, #1e293b, #ff2b2b, #38bdf8);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display:flex; justify-content:center; align-items:center;
            padding: 20px 0;
        }
        @keyframes gradientBG{ 0%{background-position:0% 50%;} 50%{background-position:100% 50%;} 100%{background-position:0% 50%;} }

        .register-card{
            background: rgba(255,255,255,0.95);
            border-radius:20px; padding:30px;
            max-width:500px; width:90%;
            box-shadow:0 15px 35px rgba(0,0,0,0.4);
        }
        h2{ margin:0 0 15px 0; color: #1b1b1b; text-align:center; font-size: 22px; }
        label { display: block; margin-top: 8px; font-size: 12px; color: #555; font-weight: bold; }
        input, select {
            width:100%; padding:10px; margin-top: 4px;
            border-radius:8px; border:1px solid #ccc; font-size:14px; box-sizing:border-box;
        }
        .btn-submit {
            width:100%; padding:14px; border:none; border-radius:10px;
            background:#ff2b2b; color:white; font-weight:bold; font-size:16px;
            cursor:pointer; margin-top: 20px;
        }

        /* AJUSTES RESPONSIVE */
        @media (max-width: 480px) {
            body { padding: 10px 0; }
            .register-card { padding: 20px; border-radius: 15px; }
            h2 { font-size: 20px; }
            .flex-mobile { flex-direction: column; gap: 0 !important; }
        }
    </style>
</head>
<body>

<div class="register-card">
    <h2>Registro de Usuario</h2>
    <form method="POST">
        <label>Cédula</label>
        <input name="cedula" type="text" value="<?= htmlspecialchars($cedula_previa) ?>" required>

        <label>Contraseña</label>
        <input name="password" type="password" required>

        <label>Nombre Completo</label>
        <input name="nombre" type="text" required>

        <label>Secretaría</label>
        <select name="secretaria" required>
            <option value="">-- Seleccione --</option>
            <?php foreach($secretarias as $sec): ?>
                <option value="<?= $sec ?>"><?= $sec ?></option>
            <?php endforeach; ?>
        </select>

        <div class="flex-mobile" style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label>Sexo</label>
                <select name="sexo">
                    <option value="H">Hombre</option>
                    <option value="M">Mujer</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label>Teléfono</label>
                <input name="telefono" type="text">
            </div>
        </div>

        <button type="submit" class="btn-submit">Registrarme</button>
    </form>
    <a href="login.php" style="display:block; text-align:center; margin-top:15px; font-size:13px; color:#555; text-decoration:none;">Volver al Login</a>
</div>

</body>
</html>