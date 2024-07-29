<?php
// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Verificar si se recibió un ID de actividad para eliminar
if(isset($_POST['id_actividad'])) {
    $db = conectarDB();

    // Recuperar el ID de la actividad a eliminar
    $id_actividad = $_POST['id_actividad'];

    try {
        // Iniciar una transacción para asegurar la integridad de los datos
        $db->beginTransaction();
        
        // Preparar y ejecutar la consulta SQL para eliminar la actividad de la tabla actividades_grupos
        $stmt = $db->prepare("DELETE FROM actividades_grupos WHERE id_actividad = :id_actividad");
        $stmt->bindParam(':id_actividad', $id_actividad);
        $stmt->execute();

        // Preparar y ejecutar la consulta SQL para eliminar la actividad de la tabla actividades
        $stmt = $db->prepare("DELETE FROM actividades WHERE id_actividad = :id_actividad");
        $stmt->bindParam(':id_actividad', $id_actividad);
        $stmt->execute();

        // Preparar y ejecutar la consulta SQL para eliminar los archivos de la actividad de la tabla archivos_actividades
        $stmt = $db->prepare("DELETE FROM archivos_actividades WHERE id_actividad = :id_actividad");
        $stmt->bindParam(':id_actividad', $id_actividad);
        $stmt->execute();

        // Confirmar la transacción
        $db->commit();

        // Enviar una respuesta JSON de éxito
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        // Si ocurre un error al eliminar la actividad, deshacer la transacción y enviar una respuesta JSON de error
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la actividad: '. $e->getMessage()]);
    }
} else {
    // Enviar una respuesta JSON de error si no se proporcionó un ID de actividad
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de actividad']);
}
?>