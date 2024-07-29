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

// Verificar si se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convertir los datos JSON recibidos en un array asociativo de PHP
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se recibieron los datos esperados
    if (isset($data["id_usuario"], $data["id_grupo"], $data["texto"])) {
        // Recibir datos del formulario
        $id_usuario = $data["id_usuario"];
        $id_grupo = $data["id_grupo"];
        $texto = $data["texto"];
        
        // Conectar a la base de datos
        $db = conectarDB();

        try {
            // Preparar la consulta SQL para insertar el comentario
            $stmt = $db->prepare("INSERT INTO Comentarios (texto, id_usuario, id_grupo) VALUES (?, ?, ?)");
            // Ejecutar la consulta con los datos proporcionados
            $stmt->execute([$texto, $id_usuario, $id_grupo]);
            // Devolver un mensaje de éxito en formato JSON
            echo json_encode(["mensaje" => "Comentario guardado exitosamente."]);
        } catch(PDOException $e) {
            // Si hay un error, devolver un mensaje de error en formato JSON
            echo json_encode(["error" => "Error al ejecutar la consulta: " . $e->getMessage()]);
        }
    } else {
        // Si faltan datos necesarios, devolver un mensaje de error en formato JSON
        echo json_encode(["error" => "Falta algún dato necesario."]);
    }
} else {
    // Si la solicitud no es de tipo POST, devolver un mensaje de error en formato JSON
    echo json_encode(["error" => "La solicitud no fue de tipo POST."]);
}
?>