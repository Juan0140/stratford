<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén los datos del formulario
    $nombreGrupo = $_POST["nombre"];
    $detallesGrupo = $_POST["detalles"];
    
    // Obtén el nombre del archivo y la ruta temporal del archivo cargado
    $nombreArchivo = $_FILES["icono"]["name"];
    $rutaTemporal = $_FILES["icono"]["tmp_name"];

    // Obtiene la extensión del archivo original
    $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

    // Define el nombre del archivo con el nombre del grupo y la extensión original
    $nombreArchivoFinal = $nombreGrupo . "." . $extension;

    // Define la ruta de destino donde se guardará el archivo (carpeta "iconos" con el nombre del grupo y la extensión original)
    $directorioDestino = "iconos/";
    $rutaDestino = $directorioDestino . $nombreArchivoFinal;

    // Mueve el archivo cargado a la carpeta de destino
    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        // El archivo se movió correctamente, ahora puedes almacenar la ruta en la base de datos
        // Prepara la consulta SQL para almacenar el nuevo grupo en la base de datos
        $consulta = "INSERT INTO grupos (nombre, icono, detalles) VALUES (:nombreGrupo, :iconoGrupo, :detallesGrupo)";

        // Intenta conectarte a la base de datos y ejecutar la consulta
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
            // Establecer el modo de error de PDO a excepción
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Preparar la consulta SQL
            $stmt = $conn->prepare($consulta);
            // Vincular parámetros
            $stmt->bindParam(':nombreGrupo', $nombreGrupo);
            $stmt->bindParam(':iconoGrupo', $rutaDestino); // Guardar la ruta del icono en la base de datos
            $stmt->bindParam(':detallesGrupo', $detallesGrupo);
            // Ejecutar la consulta
            $stmt->execute();
            // Devolver una respuesta de éxito
            $respuesta = array(
                "success" => true,
                "mensaje" => "El grupo se agregó correctamente."
            );
            echo json_encode($respuesta);
            exit();
        } catch(PDOException $e) {
            // Si hay algún error al ejecutar la consulta, devolver un mensaje de error
            $respuesta = array(
                "success" => false,
                "mensaje" => "Error al agregar el grupo: " . $e->getMessage()
            );
            echo json_encode($respuesta);
            exit();
        }
    } else {
        // Si ocurrió un error al mover el archivo, devuelve un mensaje de error
        $respuesta = array(
            "success" => false,
            "mensaje" => "Error al cargar el archivo de icono del grupo."
        );
        echo json_encode($respuesta);
        exit();
    }
} else {
    // Si no se envió el formulario correctamente, devuelve un mensaje de error
    $respuesta = array(
        "success" => false,
        "mensaje" => "El formulario no se envió correctamente."
    );
    echo json_encode($respuesta);
}
?>