<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Resto de los datos del formulario
    // Obtén los datos del formulario
    $nombre = $_POST["nombre"];
    $usuario = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];
    $profesor = $_POST["profesor"];

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

    try {
        // Establecer la conexión con la base de datos
        $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
        // Establecer el modo de error de PDO a excepción
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara la consulta SQL para almacenar en la tabla login
        if (!empty($urlImagen)) {
            // Si se proporciona una imagen
            $consultaLogin = "INSERT INTO login (name, user, pass, foto, profesor) VALUES ('$nombre', '$usuario', '$contrasenaEncriptada', '$urlImagen', $profesor)";
        } else {
            // Si no se proporciona una imagen
            $consultaLogin = "INSERT INTO login (name, user, pass, profesor) VALUES ('$nombre', '$usuario', '$contrasenaEncriptada', $profesor)";
        }

        // Ejecutar la consulta para insertar en la tabla login
        $conn->exec($consultaLogin);

        // Obtener el ID del docente insertado
        $idDocente = $conn->lastInsertId();

        // Verificar si se seleccionaron grupos
        if (isset($_POST['grupos']) && is_array($_POST['grupos'])) {
            // Recorrer los grupos seleccionados
            foreach ($_POST['grupos'] as $idGrupo) {
                // Preparar la consulta SQL para insertar en la tabla usuarios-grupos
                $consultaUsuariosGrupos = "INSERT INTO `usuarios-grupos` (id_usuario, id_grupo) VALUES ($idDocente, $idGrupo)";
                // Ejecutar la consulta para insertar en la tabla usuarios-grupos
                $conn->exec($consultaUsuariosGrupos);
            }
        }

        // Redireccionar y mostrar mensaje de éxito
        header("Location: admin.html");
        exit();
    } catch (PDOException $e) {
        // Mostrar mensaje de error
        header("Location: formulario.php?error=" . $e->getMessage());
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