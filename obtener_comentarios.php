<?php
// Establecer encabezados para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si se recibió el parámetro id_grupo en la solicitud GET
if (isset($_GET['id_grupo'])) {
    // Obtener el ID del grupo desde la solicitud GET
    $idGrupo = $_GET['id_grupo'];

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

    // Consulta SQL para obtener los comentarios del grupo junto con el nombre y la foto del usuario
    $sql = "SELECT Comentarios.id, Comentarios.texto, Comentarios.id_usuario, login.name, login.foto FROM Comentarios
        INNER JOIN login ON Comentarios.id_usuario = login.id
        WHERE Comentarios.id_grupo = $idGrupo";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        // Crear un array para almacenar los comentarios
        $comentarios = array();

        // Recorrer los resultados y almacenarlos en el array
        while ($row = $result->fetch_assoc()) {
            $comentario = array(
                'id' => $row['id'],
                'id_usuario' => $row['id_usuario'],
                'texto' => $row['texto'],
                'name' => $row['name'], // Agregar el nombre del usuario
                'foto' => $row['foto'] // Agregar la foto del usuario
            );
            array_push($comentarios, $comentario);
        }

        // Devolver los comentarios en formato JSON
        echo json_encode($comentarios);
    } else {
        // Si no se encontraron comentarios, devolver un array vacío
        echo json_encode(array());
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // Si no se proporcionó el parámetro id_grupo, devolver un mensaje de error
    echo json_encode(array('error' => 'No se proporcionó el ID del grupo'));
}
?>