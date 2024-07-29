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

$db = conectarDB();

// Get the comment ID from the URL parameter
$comentarioId = $_GET['id'];

$stmt = $db->prepare("SELECT id, texto, fecha, id_usuario, id_grupo, id_actividad FROM Comentarios WHERE id = :comentarioId");
$stmt->bindParam(":comentarioId", $comentarioId);
$stmt->execute();

$comentarioData = $stmt->fetch();

if ($comentarioData) {
    // Aquí puedes definir el formulario de respuesta HTML
    $respuestaFormHTML = '
    <form id="respuestaForm">
        <input type="hidden" name="idPadre" value="' . $comentarioId . '">
        <textarea name="texto" placeholder="Escribe tu respuesta"></textarea>
        <button type="submit">Enviar</button>
    </form>';
    
    // Imprime el formulario de respuesta HTML
    echo $respuestaFormHTML;
} else {
    // Handle the case where no comment is found with the given ID
    echo "No se encontró el comentario con ID: " . $comentarioId;
}
?>