<?php
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Recibe los datos del usuario del formulario de inicio de sesión
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepara la consulta SQL para seleccionar el usuario con el nombre de usuario proporcionado
    $stmt = $conn->prepare("SELECT * FROM login WHERE user = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    // Obtiene el resultado de la consulta
    $resultado = $stmt->fetch();

    // Verifica si se encontró un usuario con el nombre de usuario proporcionado
    if ($resultado) {
    // Verifica si la contraseña proporcionada coincide con la contraseña almacenada en la base de datos
        if (password_verify($contrasena, $resultado['pass'])) {
            $id = $resultado['id']; // Obtener el ID del usuario
            $name = $resultado['name'];
            // La contraseña es correcta, el inicio de sesión es exitoso
            if ($resultado['admin'] == 1) {
                // Si el usuario es administrador, redirige a la página de admin.html
                header('Location: /admin.html?id=' . $id . '&name=' . rawurlencode($name));
                exit;
            } else if ($resultado['profesor'] == 1) {
                // Enviar los grupos asociados al usuario como respuesta JSON
                echo json_encode($grupos_usuario);
                // Si el usuario es profesor, redirige a la página de profesor.html
                header('Location: /profesor.html?id=' . $id . '&name=' . rawurlencode($name));
                exit;
            } else {
                // Si el usuario no es administrador ni profesor, redirige a la página de alumno.html
                header('Location: /alumno.html?id=' . $id . '&name=' . rawurlencode($name));
                exit;
            }
        } else {
            // La contraseña no coincide, muestra un mensaje de error
            http_response_code(401);
            echo "Contraseña incorrecta";
        }
    } else {
        // No se encontró ningún usuario con el nombre de usuario proporcionado
        http_response_code(401);
        echo "Usuario incorrecto";
    }
} catch(PDOException $e) {
    // Captura cualquier excepción que pueda ocurrir durante la conexión o la ejecución de la consulta SQL
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>