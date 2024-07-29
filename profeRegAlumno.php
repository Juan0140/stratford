<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

$previous_page = $_SERVER['HTTP_REFERER'] ?? 'profeRegAlumno.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Resto de los datos del formulario
    $nombre = $_POST["nombre"];
    $usuario = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];
    $nombreGrupo = $_POST["nombre"];

    // Encripta la contraseña
    $contrasenaEncriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Inicializa la URL de la imagen
    $urlImagen = "";

    // Verifica si se proporcionó una imagen
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == UPLOAD_ERR_OK) {
        // Directorio donde se guardarán las imágenes
        $directorio = "fotos/";

        // Nombre del archivo original
        $nombreArchivoOriginal = $_FILES["foto"]["name"];
        
        // Obtener la extensión del archivo
        $extension = pathinfo($nombreArchivoOriginal, PATHINFO_EXTENSION);
        
        // Genera un nombre único para el archivo
        $nombreAleatorio = uniqid() . '.' . $extension;
        
        // Ruta donde se guardará la imagen
        $ruta = $directorio . $nombreAleatorio;
        
        // Guarda la imagen en el servidor
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta)) {
            // URL de la imagen
            $urlImagen = "/fotos/" . $nombreAleatorio;
        } else {
            // Error al mover el archivo
            $respuesta = array(
                "success" => false,
                "mensaje" => "¡Hubo un error al subir la imagen!"
            );
            echo json_encode($respuesta);
            exit();
        }
    }
    
    // Get the value of the "profesor" field
    $profesor = $_POST["profesor"];

    // Prepara la consulta SQL para almacenar en la base de datos
    if (!empty($urlImagen)) {
        // Si se proporciona una imagen
        $consulta = "INSERT INTO login (name, user, pass, foto, profesor) VALUES ('$nombre', '$usuario', '$contrasenaEncriptada', '$urlImagen', $profesor)";
    } else {
        // Si no se proporciona una imagen
        $consulta = "INSERT INTO login (name, user, pass, profesor) VALUES ('$nombre', '$usuario', '$contrasenaEncriptada', $profesor)";
    }

    // Aquí deberías ejecutar la consulta SQL utilizando tu método preferido (PDO, mysqli, etc.)
    // Por ejemplo, con PDO:
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
        // Establecer el modo de error de PDO a excepción
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ejecutar la consulta
        $conn->exec($consulta);
        
        // Procesar los checkboxes seleccionados
        if(isset($_POST['grupos'])) {
            $grupos_seleccionados = $_POST['grupos'];
            
            foreach($grupos_seleccionados as $grupo_id) {
                // Preparar la consulta para insertar en la tabla 'usuarios-grupos'
                $consulta_usuarios_grupos = "INSERT INTO `usuarios-grupos` (id_usuario, id_grupo) VALUES ((SELECT MAX(id) FROM login), $grupo_id)";
                
                // Ejecutar la consulta SQL para insertar en 'usuarios-grupos'
                $conn->exec($consulta_usuarios_grupos);
            }
        }
        
        // Redireccionar y pasar el ID y nombre de sesión como parámetros en la URL
        header("Location: $previous_page");
        exit();
    } catch(PDOException $e) {
        // Mostrar mensaje de error
        $error_message = urlencode($e->getMessage());
        header("Location: $previous_page?error=$error_message");
        exit();
    }

} else {
    // Si no se envió el formulario correctamente
    $respuesta = array(
        "success" => false,
        "mensaje" => "¡El formulario no se envió correctamente!"
    );
    echo json_encode($respuesta);
}
?>