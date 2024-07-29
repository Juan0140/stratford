<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID del grupo a eliminar desde la solicitud
    $idGrupo = $_POST["id"];

    // Preparar la consulta SQL para eliminar el grupo de la base de datos
    $consulta = "DELETE FROM grupos WHERE id = :idGrupo";

    // Intentar conectarse a la base de datos y ejecutar la consulta
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
        // Establecer el modo de error de PDO a excepción
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Preparar la sentencia SQL
        $stmt = $conn->prepare($consulta);
        // Vincular parámetros
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        // Ejecutar la consulta
        $stmt->execute();

        // Devolver una respuesta de éxito
        $respuesta = array(
            "success" => true,
            "mensaje" => "El grupo se eliminó correctamente."
        );
        echo json_encode($respuesta);
        exit();
    } catch(PDOException $e) {
        // Si hay algún error al ejecutar la consulta, devolver un mensaje de error
        $respuesta = array(
            "success" => false,
            "mensaje" => "Error al eliminar el grupo: " . $e->getMessage()
        );
        echo json_encode($respuesta);
        exit();
    }
} else {
    // Si no se envió la solicitud correctamente, devuelve un mensaje de error
    $respuesta = array(
        "success" => false,
        "mensaje" => "La solicitud no se envió correctamente."
    );
    echo json_encode($respuesta);
}
?>