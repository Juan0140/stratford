<?php
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Recibe el ID de grupo del cuerpo de la solicitud
$id_grupo = $_GET['id_grupo'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obtener los alumnos del grupo que no sean administradores ni profesores
    $stmt = $conn->prepare("SELECT l.id,l.foto,l.name
                            FROM `usuarios-grupos` ug
                            INNER JOIN login l ON ug.id_usuario = l.id
                            WHERE ug.id_grupo = :id_grupo AND l.admin = 0 AND l.profesor = 0");
    $stmt->bindParam(':id_grupo', $id_grupo);
    $stmt->execute();

    // Obtiene los resultados de la consulta como un array
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if (count($alumnos) > 0) {
        // Devuelve los nombres de los alumnos como una respuesta JSON
        echo json_encode($alumnos);
    } else {
        // Si no hay resultados, mostrar un mensaje indicando que el grupo no tiene alumnos inscritos
        echo json_encode(array('mensaje' => 'Este grupo aún no tiene alumnos inscritos.', 'id_grupo' => $id_grupo));
    }
} catch(PDOException $e) {
    // Si ocurre un error durante la ejecución de la consulta SQL, devuelve un mensaje de error
    http_response_code(500);
    echo json_encode(array('error' => 'Error: ' . $e->getMessage()));
}
?>