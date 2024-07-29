<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Create connection
$conn = new mysqli($servername, $username, $password, $basededatos);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $id = $_POST["id"];
    $name = $_POST["name"];
    $usuario = $_POST["usuario"];

    // Initialize variables for password and image
    $pass_update = false;
    $image_update = false;

    // Check if the password field is provided and not empty
    if (isset($_POST["pass"]) && $_POST["pass"] !== '') {
        $pass = $_POST["pass"];
        $contrasenaEncriptada = password_hash($pass, PASSWORD_DEFAULT);
        $pass_update = true; // Flag to indicate password update
    }

    // Verifica si se proporcionó una imagen
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == UPLOAD_ERR_OK) {
        // Directorio donde se guardarán las imágenes
        $directorio = "fotos/";

        // Nombre del archivo original
        $nombreArchivoOriginal = $_FILES["foto"]["name"];

        // Genera un nombre único para el archivo
        $nombreAleatorio = uniqid() . '_' . $nombreArchivoOriginal;

        // Ruta donde se guardará la imagen
        $ruta = $directorio . $nombreAleatorio;

        // Guarda la imagen en el servidor
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $ruta)) {
            // URL de la imagen
            $urlImagen = "https://chroma-fix.com/" . $ruta;
            $image_update = true; // Flag to indicate image update
        } else {
            // Error al mover el archivo
            $respuesta = array(
                "success" => false,
                "message" => "¡Hubo un error al subir la imagen!"
            );
            echo json_encode($respuesta);
            exit();
        }
    }

    // Construct the update query based on conditions
    if ($pass_update && $image_update) {
        $query = "UPDATE login SET name='$name', user='$usuario', pass='$contrasenaEncriptada', foto='$urlImagen' WHERE id=$id";
    } elseif ($pass_update) {
        $query = "UPDATE login SET name='$name', user='$usuario', pass='$contrasenaEncriptada' WHERE id=$id";
    } elseif ($image_update) {
        $query = "UPDATE login SET name='$name', user='$usuario', foto='$urlImagen' WHERE id=$id";
    } else {
        $query = "UPDATE login SET name='$name', user='$usuario' WHERE id=$id";
    }

    // Execute query
    if ($conn->query($query) === TRUE) {
        // If successful, return a success message
        echo json_encode(array("success" => true, "message" => "Registro actualizado exitosamente."));
    } else {
        // If not successful, return an error message
        echo json_encode(array("success" => false, "message" => "Error: " . $query . "<br>" . $conn->error));
    }

    // Close connection
    $conn->close();
}
?>