<?php
// Verificar si se recibió el parámetro id del comentario a borrar
if (isset($_GET['id'])) {
    // Obtener el ID del comentario desde la solicitud GET
    $idComentario = $_GET['id'];

    // Conectar a la base de datos (reemplaza los valores con los de tu conexión)
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

    $conn = new mysqli($servername, $username, $password, $basededatos);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }

    // Consulta SQL para eliminar el comentario de la base de datos
    $sql = "DELETE FROM Comentarios WHERE id = $idComentario";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        // Si se elimina correctamente, enviar una respuesta JSON con el mensaje de éxito
        echo json_encode(array('success' => true, 'message' => 'Comentario eliminado correctamente'));
    } else {
        // Si ocurre un error, enviar una respuesta JSON con el mensaje de error
        echo json_encode(array('success' => false, 'message' => 'Error al eliminar el comentario: ' . $conn->error));
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // Si no se proporcionó el ID del comentario, enviar una respuesta JSON con un mensaje de error
    echo json_encode(array('success' => false, 'message' => 'ID de comentario no proporcionado.'));
}
?>