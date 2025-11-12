<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión</title>
<link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="login-box">
    <h2>Iniciar Sesión</h2>

    <form action="../php/login_procesar.php" method="POST">

        <input type="text" name="correo" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

    <?php if(isset($_GET['error'])){ echo "<p style='color:red;'>Usuario o contraseña incorrectos</p>"; } ?>

</div>

</body>
</html>

