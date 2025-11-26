<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado"){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Cliente</title>
<link rel="stylesheet" href="../CSS/estilo_panel.css">
<style>
.form-box{
    background: white;
    padding: 30px;
    max-width: 550px;
    margin: 40px auto;
    border-radius: 8px;
    border: 1px solid #dcd6c8;
    font-family: 'Montserrat', sans-serif;
}
.form-box h2{
    font-family: 'Lora', serif;
    font-size: 26px;
    color: #333;
    text-align: center;
    margin-bottom: 25px;
}
.form-box input{
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    margin-bottom: 18px;
    border: 1px solid #b9b3a8;
    border-radius: 4px;
}
button{
    background:#004aad;
    color: white;
    border: none;
    padding: 12px 20px;
    width: 100%;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}
button:hover{
   background:#00337a;
}
</style>
</head>

<body>

<?php include "../inc/header.php"; ?>
<?php include "../inc/sidebar.php"; ?>


<div class="content">
    <div class="form-box">
        <h2>Registrar Nuevo Cliente</h2>
        <?php if (isset($_GET['msg'])): ?>
  <?php
    $mensaje = "";
    $tipo = "info";

    switch ($_GET['msg']) {
      case 'cli_ok':
        $mensaje = "✅ Cliente registrado correctamente.";
        $tipo = "success";
        break;
      case 'cli_pass':
        $mensaje = "⚠️ Las contraseñas no coinciden.";
        $tipo = "warning";
        break;
      case 'cli_faltan':
        $mensaje = "⚠️ Faltan datos obligatorios.";
        $tipo = "warning";
        break;
      case 'cli_sql_prep':
        $mensaje = "❌ Error interno preparando el registro.";
        $tipo = "error";
        break;
      case 'cli_err':
        $mensaje = "❌ Error al guardar el cliente.";
        $tipo = "error";
        break;
    }
  ?>
  <div class="mensaje <?= $tipo ?>" id="mensaje-notificacion">
    <?= $mensaje ?>
  </div>
<?php endif; ?>

<style>
.mensaje {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 12px 18px;
  border-radius: 8px;
  font-family: 'Montserrat', sans-serif;
  font-weight: 500;
  font-size: 15px;
  color: white;
  animation: fadeIn 0.5s ease, fadeOut 0.5s ease 3s forwards;
  z-index: 9999;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.mensaje.success { background-color: #28a745; }  
.mensaje.warning { background-color: #ffc107; color: #212529; }
.mensaje.error   { background-color: #dc3545; }  
@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, -20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
@keyframes fadeOut {
  from { opacity: 1; transform: translate(-50%, 0); }
  to { opacity: 0; transform: translate(-50%, -20px); }
}
</style>

<script>
setTimeout(() => {
  const msg = document.getElementById('mensaje-notificacion');
  if (msg) msg.remove();
}, 3500);
</script>

        <form action="../PHP/guardar_cliente.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="ap_pat" placeholder="Apellido Paterno" required>
            <input type="text" name="ap_mat" placeholder="Apellido Materno" required>
            <input type="text" name="rfc" placeholder="RFC del cliente" required>
            <input type="text" name="cp" placeholder="Código Postal" required>
            <input type="text" name="direccion" placeholder="Dirección completa" required>
            <input type="text" name="telefono" placeholder="Teléfono o celular" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
            <button type="submit">Guardar Cliente</button>
        </form>
    </div>
</div>

</body>
</html>
