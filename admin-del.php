<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Verificar si se recibió el ID a eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    // Obtener el ID a eliminar desde los datos POST
    $id = $_POST['id'];

    // Realizar la conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $basededatos);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Consulta SQL para eliminar las entradas relacionadas en la tabla usuarios-grupos
        $sql_usuarios_grupos = "DELETE FROM `usuarios-grupos` WHERE id_usuario = $id";

        // Ejecutar la consulta SQL para eliminar las entradas relacionadas en la tabla usuarios-grupos
        if ($conn->query($sql_usuarios_grupos) !== TRUE) {
            // Si hubo un error durante la eliminación de las entradas relacionadas en la tabla usuarios-grupos,
            // lanzar una excepción
            throw new Exception("Error al eliminar las entradas relacionadas en usuarios-grupos");
        }

        // Consulta SQL para eliminar el registro de la tabla login
        $sql_login = "DELETE FROM login WHERE id = $id";

        // Ejecutar la consulta SQL para eliminar el registro de la tabla login
        if ($conn->query($sql_login) !== TRUE) {
            // Si hubo un error durante la eliminación del registro en la tabla login, lanzar una excepción
            throw new Exception("Error al eliminar el registro en la tabla login");
        }

        // Confirmar la transacción si todas las consultas se ejecutaron con éxito
        $conn->commit();

        // Devolver una respuesta de éxito al cliente
        echo json_encode(array("success" => true));
    } catch (Exception $e) {
        // Si se lanzó una excepción durante la transacción, revertir la transacción
        $conn->rollback();

        // Devolver un mensaje de error al cliente
        echo json_encode(array("success" => false, "error" => $e->getMessage()));
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // Si no se recibió el ID, devolver un mensaje de error al cliente
    echo json_encode(array("success" => false, "error" => "ID a eliminar no recibido"));
}
?>