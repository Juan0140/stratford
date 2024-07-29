<?php
// Verificar si se recibió el parámetro id del comentario a editar
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

    // Consulta SQL para obtener el texto actual del comentario
    $sql = "SELECT texto FROM Comentarios WHERE id = $idComentario";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    // Verificar si se encontró el comentario
    if ($result->num_rows == 1) {
        // Obtener el texto actual del comentario
        $row = $result->fetch_assoc();
        $textoActual = $row['texto'];
    } else {
        echo "El comentario no existe.";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    echo "ID de comentario no proporcionado.";
}
?>