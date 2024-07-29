<?php
// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función para conectar a la base de datos
function conectarDB() {
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
        // Establecer el modo de error de PDO a excepción
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        // Si ocurre un error al conectar a la base de datos, mostrar un mensaje de error
        die("Error al conectar a la base de datos: ". $e->getMessage());
    }
}

$db = conectarDB();

// Verificar si se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos esperados
    if (isset($_POST["id_actividad"], $_POST["id_alumno"], $_POST["id_grupo"], $_FILES["archivoRespuesta"])) {
        // Recibir datos del formulario
        $id_actividad = $_POST["id_actividad"];
        $id_alumno = $_POST["id_alumno"];
        $id_grupo = $_POST["id_grupo"];
        $comentario = $_POST["comentario"];
        
        // Verificar si ya existe un documento para la actividad y el alumno específicos
        $stmt = $db->prepare("SELECT id_documento FROM Documentos WHERE id_actividad = ? AND id_alumno = ?");
        $stmt->execute([$id_actividad, $id_alumno]);
        $documento_existente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si existe un documento, actualizarlo; de lo contrario, insertarlo
        if ($documento_existente) {
            // Iterar sobre cada archivo recibido
            foreach ($_FILES["archivoRespuesta"]["name"] as $index => $nombre_archivo_original) {
                $nombre_extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION); // Obtener la extensión del archivo
                $nombre_unico = uniqid() . '.' . $nombre_extension; // Generar un nombre único con la misma extensión
                $ruta_archivo = "Documentos/" . $nombre_unico;
                
                // Mover el archivo cargado a la ruta deseada con el nombre único
                if (move_uploaded_file($_FILES["archivoRespuesta"]["tmp_name"][$index], $ruta_archivo)) {
                    try {
                        // Actualizar el registro existente en la tabla de documentos con el nuevo archivo y el comentario
                        $stmt = $db->prepare("UPDATE Documentos SET nombre_archivo = ?, ruta_archivo = ?, comentario = ? WHERE id_documento = ?");
                        $stmt->execute([$nombre_unico, $ruta_archivo, $comentario, $documento_existente["id_documento"]]);
                    } catch(PDOException $e) {
                        // Si hay un error, devolver un mensaje de error en formato JSON
                        echo json_encode(["error" => "Error al ejecutar la consulta: " . $e->getMessage()]);
                        return;
                    }
                } else {
                    // Si hay un error al mover el archivo, devolver un mensaje de error en formato JSON
                    echo json_encode(["error" => "Error al mover el archivo."]);
                    return;
                }
            }
        } else {
            // Si no existe un documento, insertarlo
            try {
                // Iterar sobre cada archivo recibido
                foreach ($_FILES["archivoRespuesta"]["name"] as $index => $nombre_archivo_original) {
                    $nombre_extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION); // Obtener la extensión del archivo
                    $nombre_unico = uniqid() . '.' . $nombre_extension; // Generar un nombre único con la misma extensión
                    $ruta_archivo = "Documentos/" . $nombre_unico;
                    
                    // Mover el archivo cargado a la ruta deseada con el nombre único
                    if (move_uploaded_file($_FILES["archivoRespuesta"]["tmp_name"][$index], $ruta_archivo)) {
                        // Preparar la consulta SQL
                        $stmt = $db->prepare("INSERT INTO Documentos (id_actividad, id_alumno, id_grupo, nombre_archivo, ruta_archivo, comentario) VALUES (?, ?, ?, ?, ?, ?)");
                        // Ejecutar la consulta con los datos proporcionados
                        $stmt->execute([$id_actividad, $id_alumno, $id_grupo, $nombre_unico, $ruta_archivo, $comentario]);
                    } else {
                        // Si hay un error al mover el archivo, devolver un mensaje de error en formato JSON
                        echo json_encode(["error" => "Error al mover el archivo."]);
                        return;
                    }
                }
            } catch(PDOException $e) {
                // Si hay un error, devolver un mensaje de error en formato JSON
                echo json_encode(["error" => "Error al ejecutar la consulta: " . $e->getMessage()]);
                return;
            }
        }
        
        // Si se procesaron todos los archivos exitosamente, devolver un mensaje de éxito en formato JSON
        echo json_encode(["mensaje" => "Documentos subidos exitosamente."]);
    } else {
        // Si faltan datos necesarios, devolver un mensaje de error en formato JSON
        echo json_encode(["error" => "Falta algún dato necesario."]);
    }
} else {
    // Si la solicitud no es de tipo POST, devolver un mensaje de error en formato JSON
    echo json_encode(["error" => "La solicitud no fue de tipo POST."]);
}
?>