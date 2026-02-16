<?php
require "../config/db.php";

if ($_POST) {

    $stmt = $conn->prepare("
        INSERT INTO users 
        (cedula, nombre, secretaria, dependencia, sexo, correo, telefono, tipo_funcionario)
        VALUES (?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $_POST['cedula'],
        $_POST['nombre'],
        $_POST['area'], // secretaria
        $_POST['area'], // dependencia (mismo valor)
        $_POST['sexo'],
        $_POST['correo'],
        $_POST['telefono'],
        $_POST['tipo_funcionario']
    ]);
header("Location: /lms/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Usuario</title>

<style>
*{
    box-sizing:border-box;
    font-family:"Segoe UI", Arial, sans-serif;
}

body{
    background:#f5f5f5;
    margin:0;
}

.form-container{
    max-width:650px;
    margin:60px auto;
    background:#fff;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    overflow:hidden;
}

.form-header{
    background:#b91c1c;
    color:#fff;
    padding:20px;
    font-size:20px;
    font-weight:600;
    text-align:center;
}

form{
    padding:25px;
}

label{
    display:block;
    font-size:14px;
    margin-bottom:6px;
    font-weight:600;
}

input, select{
    width:100%;
    padding:10px 12px;
    margin-bottom:18px;
    border:1px solid #d1d5db;
    border-radius:6px;
    font-size:14px;
}

input:focus, select:focus{
    outline:none;
    border-color:#b91c1c;
    box-shadow:0 0 0 2px rgba(185,28,28,.15);
}

/* AUTOCOMPLETE */
.autocomplete{
    position:relative;
}

.lista{
    position:absolute;
    top:100%;
    left:0;
    right:0;
    background:#fff;
    border:1px solid #d1d5db;
    max-height:260px;
    overflow:auto;
    display:none;
    z-index:99;
}

.lista div{
    padding:10px;
    cursor:pointer;
}

.lista div:hover{
    background:#fee2e2;
}

button{
    width:100%;
    padding:12px;
    background:#b91c1c;
    color:#fff;
    border:none;
    border-radius:6px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:#991b1b;
}

.form-footer{
    text-align:center;
    font-size:12px;
    color:#6b7280;
    padding-bottom:15px;
}
</style>
</head>

<body>

<div class="form-container">

<div class="form-header">Registro de Usuario</div>

<form method="POST">

<label>Cédula</label>
<input name="cedula" value="<?= $_GET['c'] ?? '' ?>" required>

<label>Nombre completo</label>
<input name="nombre" required>

<label>Área / Dependencia / Secretaría</label>
<div class="autocomplete">
    <input 
        type="text" 
        id="areaTexto"
        placeholder="Seleccione el Área / Dependencia / Secretaría"
        autocomplete="off"
        required
    >
    <input type="hidden" name="area" id="areaValor">

    <div class="lista" id="listaAreas">
       <div>Despacho del Alcalde</div>
<div>Oficina de Control Interno de Gestión</div>
<div>Oficina de Control Interno Disciplinario de Instrucción</div>
<div>Oficina de Gestión Jurídica</div>
<div>Oficina de Tecnologías de la Información y las Comunicaciones</div>
<div>Oficina de Prensa, Comunicaciones y Protocolo</div>
<div>Consejería de Asuntos Religiosos</div>
<div>Consejería de Asuntos Sociales</div>
<div>Oficina de Migración y Frontera</div>
<div>Oficina de Promoción Turística</div>
<div>Oficina Emprendimiento y Acceso al Crédito – Banco del Progreso</div>
<div>Secretaría General</div>
<div>Oficina de Talento Humano</div>
<div>Área de Trabajo de Pensiones</div>
<div>Área de Trabajo de Desarrollo del Talento Humano</div>
<div>Área de Trabajo de Seguridad y Salud en el Trabajo</div>
<div>Área de Trabajo de Gestión Contractual</div>
<div>Área de Trabajo de Almacén e Inventario</div>
<div>Área de Trabajo de Gestión Documental</div>
<div>Oficina de Relacionamiento con el Ciudadano</div>
<div>Secretaría de Hacienda</div>
<div>Subsecretaría Financiera y Gestión Presupuestal</div>
<div>Subsecretaría de Rentas e Impuestos</div>
<div>Dirección Técnica de Fiscalización y Liquidación Tributaria</div>
<div>Área de Trabajo de Discusión</div>
<div>Subsecretaría de Cobro Coactivo</div>
<div>Subsecretaría de Gestión Catastral Multipropósito</div>
<div>Oficina del Tesoro</div>
<div>Oficina de Contabilidad</div>
<div>Secretaría de Planeación y Desarrollo Territorial</div>
<div>Subsecretaría de Proyección Socio Económica</div>
<div>Área de Trabajo de Planeación Territorial</div>
<div>Área de Trabajo de Desempeño Institucional</div>
<div>Oficina de Caracterización Socio Económica</div>
<div>Subsecretaría de Ordenamiento Territorial</div>
<div>Área de Trabajo de Control Físico</div>
<div>Área de Trabajo de Desarrollo Físico</div>
<div>Oficina de Gestión y Supervisión de Servicios Públicos Domiciliarios</div>
<div>Secretaría Valorización y Plusvalía</div>
<div>Secretaría Privada</div>
<div>Secretaría de Gobierno</div>
<div>Área de Trabajo de Control Urbano y Espacio Público</div>
<div>Subsecretaría de Desarrollo Comunitario</div>
<div>Subsecretaría de Acceso a la Justicia y Derechos Humanos</div>
<div>Área de Trabajo de Protección y Derechos Humanos</div>
<div>Área de Trabajo de Justicia Familiar</div>
<div>Área de Trabajo de Resolución de Conflictos</div>
<div>Dirección Técnica de Bienestar Animal</div>
<div>Secretaría de Seguridad Ciudadana</div>
<div>Subsecretaría de Gestión Institucional en Seguridad</div>
<div>Oficina para la Vigilancia y Control y Prevención del Delito</div>
<div>Área de Trabajo de Observatorio de Seguridad, Convivencia y Derechos Humanos</div>
<div>Secretaría de Movilidad</div>
<div>Subsecretaría de Tránsito y Transporte</div>
<div>Área de Trabajo de Inspección de Tránsito y Transporte</div>
<div>Área de Trabajo de Grupo de Control Vial</div>
<div>Secretaría de Bienestar Social</div>
<div>Área de Trabajo de Infancia y Adolescencia</div>
<div>Área de Trabajo de Discapacidad</div>
<div>Área de Trabajo de Tercera Edad</div>
<div>Área de Trabajo de Asuntos Poblacionales</div>
<div>Subsecretaría de la Juventud</div>
<div>Oficina para la Prosperidad Social</div>
<div>Secretaría de Educación</div>
<div>Subsecretaría de Desarrollo Educativo</div>
<div>Área de Trabajo de Cobertura Educativa</div>
<div>Área de Trabajo de Infraestructura Educativa</div>
<div>Área de Trabajo de Permanencia Escolar</div>
<div>Subsecretaría de Gestión Pedagógica</div>
<div>Área de Trabajo de Calidad Educativa</div>
<div>Oficina de Talento Humano Educativo</div>
<div>Dirección Financiera en Educación</div>
<div>Secretaría de Salud</div>
<div>Subsecretaría de Salud Pública</div>
<div>Área de Trabajo de Programas Esenciales de Salud Pública</div>
<div>Área de Trabajo de Gestión del Conocimiento en Salud</div>
<div>Subsecretaría de Acceso a los Servicios de Salud</div>
<div>Área de Trabajo de Aseguramiento</div>
<div>Área de Trabajo de Promoción Social y Salud de los Trabajadores</div>
<div>Subsecretaría de Gestión Institucional en Salud</div>
<div>Área de Trabajo de Planeación Institucional en Salud</div>
<div>Área de Trabajo de Gestión Jurídica en Salud</div>
<div>Dirección Financiera en Salud</div>
<div>Secretaría de Cultura y Patrimonio</div>
<div>Subsecretaría de Artes y Fomento</div>
<div>Área de Trabajo de Arte y Patrimonio</div>
<div>Área de Trabajo de Lectura y Escritura</div>
<div>Secretaría de Equidad, Género y Mujer</div>
<div>Área de Trabajo de Expresiones Sexuales Diversas</div>
<div>Área de Trabajo de Mujer</div>
<div>Área de Trabajo del Sistema del Cuidado</div>
<div>Secretaría de Víctimas, Paz y Posconflicto</div>
<div>Área de Trabajo de Reincorporados</div>
<div>Área de Trabajo de Cultura de Paz</div>
<div>Dirección Técnica de Atención Integral a Víctimas</div>
<div>Secretaría de Desarrollo Rural y Agropecuario</div>
<div>Área de Trabajo de Asistencia Técnica Agropecuaria</div>
<div>Área de Trabajo de Productividad Rural</div>
<div>Secretaría de Desarrollo Económico y Competitividad</div>
<div>Subsecretaría de Desarrollo Empresarial</div>
<div>Área de Trabajo de Empleabilidad</div>
<div>Área de Trabajo de Economía Circular</div>
<div>Dirección Técnica de Internacionalización</div>
<div>Secretaría de Infraestructura</div>
<div>Subsecretaría de Gestión Técnica de Proyectos</div>
<div>Área de Trabajo de Diseño de Obra</div>
<div>Área de Trabajo de Supervisión Técnica</div>
<div>Área de Trabajo de Planeación y Contratación</div>
<div>Secretaría de Gestión del Riesgo de Desastres</div>
<div>Área de Trabajo de Conocimiento y la Reducción del Riesgo</div>
<div>Dirección Técnica de Manejo del Riesgo</div>
<div>Secretaría de Medio Ambiente y Sostenibilidad</div>
<div>Área de Trabajo de Recursos Naturales y Biodiversidad</div>
<div>Secretaría de Hábitat</div>
<div>Área de Trabajo de Vivienda</div>
<div>Área de Trabajo de Titulación de Bienes Fiscales</div>

    </div>
</div>

<label>Sexo</label>
<select name="sexo" required>
    <option value="">Seleccione</option>
    <option value="H">Hombre</option>
    <option value="M">Mujer</option>
    <option value="OTRO">Otro</option>
</select>

<label>Correo electrónico</label>
<input type="email" name="correo" required>

<label>Teléfono</label>
<input name="telefono" required>

<label>Tipo de funcionario</label>
<select name="tipo_funcionario" required>
    <option value="">Seleccione</option>
    <option value="CONTRATISTA">Contratista</option>
    <option value="FUNCIONARIO PUBLICO">Funcionario Público</option>
</select>

<button>Guardar Usuario</button>

</form>

<div class="form-footer">
Sistema de Gestión Institucional
</div>

</div>

<script>
const input = document.getElementById("areaTexto");
const hidden = document.getElementById("areaValor");
const lista = document.getElementById("listaAreas");
const items = lista.querySelectorAll("div");

input.addEventListener("focus", () => lista.style.display = "block");

input.addEventListener("input", () => {
    const f = input.value.toLowerCase();
    lista.style.display = "block";
    items.forEach(i => {
        i.style.display = i.textContent.toLowerCase().includes(f)
            ? "block" : "none";
    });
});

items.forEach(i => {
    i.addEventListener("click", () => {
        input.value = i.textContent;
        hidden.value = i.textContent;
        lista.style.display = "none";
    });
});

document.addEventListener("click", e => {
    if (!e.target.closest(".autocomplete")) {
        lista.style.display = "none";
    }
});
</script>

</body>
</html>
