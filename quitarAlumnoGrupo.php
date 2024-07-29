<?php
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Recibe los datos del alumno y del grupo
$idUsuario = $_POST['idUsuario'];
$idGrupo = $_POST['idGrupo'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para eliminar el registro del alumno en la tabla usuarios-grupos
    $stmt = $conn->prepare("DELETE FROM `usuarios-grupos` WHERE id_usuario = :idUsuario AND id_grupo = :idGrupo");
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->bindParam(':idGrupo', $idGrupo);
    $stmt->execute();

    // Devolver una respuesta exitosa si la eliminación fue exitosa
    echo json_encode(array('success' => true, 'mensaje' => 'El alumno ha sido eliminado correctamente.'));
} catch(PDOException $e) {
    // Si ocurre un error durante la ejecución de la consulta SQL, devuelve un mensaje de error
    http_response_code(500);
    echo json_encode(array('success' => false, 'error' => 'Error: ' . $e->getMessage()));
}
?>