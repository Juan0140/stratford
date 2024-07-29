<?php
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta SQL para obtener los detalles del grupo
    $stmt = $conn->prepare("SELECT * FROM `grupos`");
    $stmt->execute();
    
    // Obtiene todos los resultados de la consulta como un array asociativo
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Devuelve los detalles de los grupos como una respuesta JSON
    echo json_encode($grupos);
} catch(PDOException $e) {
    // Si ocurre un error durante la ejecución de la consulta SQL, devuelve un mensaje de error
    http_response_code(500);
    echo json_encode(array('error' => 'Error: ' . $e->getMessage()));
}
?>