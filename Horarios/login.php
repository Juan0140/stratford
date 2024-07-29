<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="build/css/index.css">
    <link rel="stylesheet" href="build/css/normalize.css">
    <script src="build/js/login.js"></script>
</head>
<body>
    <main class="contenedor-small">
        <div class="contenido-login">
            <h1>Login Horarios y Profesores</h1>
            <div class="image-login centrar-div">
                <img src="https://stratfordlernen.com/images/de1a56e713384c6ad4a7bba0d040b881.svg" alt="Logo">
            </div>
            <form action="" class="formulario" method="post">
                <div class="campo">
                    <label for="usuario">Usuario</label>
                    <input type="text" name="usuario" placeholder="Teclea tu usuario" id="usuario" required>
                </div>
                <div class="campo">
                    <div class="contra">
                        <label for="password">Contraseña</label>
                        <p id="toggle-password" class="mostrar-contra"> 
                            <img src="build/img/ojo.png" alt="" id="ojo">
                            <img src="build/img/ver.png" alt="" id="ver" class="ocultar">
                            <span id="toggle-text">Mostrar Contraseña</span>
                        </p>
                    </div>
                    
                    <input type="password" name="password" placeholder="Teclea tu contraseña" id="password" required>
                </div>
                <input type="submit" value="Iniciar Sesión" class="boton">
            </form>
        </div>
    </main>

</body>
</html>
