<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión - RIFCU</title>
<link rel="stylesheet" href="../css/login.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

<!-- NAVBAR SUPERIOR -->
<nav class="navbar">
    <div class="nav-title">⚖️ Sistema RIFCU — García & Asociados</div>
</nav>

<!-- CONTENEDOR TOTAL -->
<div class="main">

    <div class="login-container">

        <h1 class="login-title">Iniciar Sesión</h1>
        <p class="login-subtitle">Accede con tu cuenta registrada</p>

        <form action="../php/login_procesar.php" method="POST" class="login-form">

            <div class="input-group">
                <label>Correo electrónico</label>
                <input type="text" name="correo" placeholder="ejemplo@gmail.com" required>
            </div>

            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="•••••••" required>
            </div>

            <button type="submit" class="btn-login">Ingresar</button>

        </form>

        <?php if(isset($_GET['error'])): ?>
            <p class="error-msg">❌ Usuario o contraseña incorrectos</p>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
