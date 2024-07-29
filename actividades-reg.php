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

// Validar y limpiar los datos del formulario
$nombreActividad = htmlspecialchars($_POST['nombre']);
$fechaEntrega = htmlspecialchars($_POST['fecha_entrega']);
$descripcion = htmlspecialchars($_POST['descripcion']);

// Directorio donde se guardarán los archivos
$directorio = "actividades/";

// Obtener información de los archivos subidos
$documentos = $_FILES["documento"];

try {
    // Obtener el id_grupo (suponiendo que se pasa como un parámetro en la solicitud POST)
    $idGrupo = htmlspecialchars($_POST['id_grupo']);
    // Insertar la actividad en la base de datos
    $stmt = $db->prepare('INSERT INTO actividades (nombre, fecha_entrega, descripcion) VALUES (?,?,?)');
    $stmt->execute([$nombreActividad, $fechaEntrega, $descripcion]);

    // Obtener el ID de la actividad insertada
    $idActividad = $db->lastInsertId();
    
    // Insertar el registro en la tabla actividades_grupos
    $stmt = $db->prepare('INSERT INTO actividades_grupos (id_actividad, id_grupo) VALUES (?, ?)');
    $stmt->execute([$idActividad, $idGrupo]);

    // Manejar múltiples archivos
    foreach ($documentos['tmp_name'] as $key => $tmp_name) {
        $nombreArchivoOriginal = basename($documentos['name'][$key]);
        $nombreArchivo = uniqid() . '_' . $nombreArchivoOriginal;
        $ruta = $directorio . $nombreArchivo;
        if (move_uploaded_file($tmp_name, $ruta)) {
            // Construir la ruta completa del archivo
            $urlArchivo = "https://stratrooms.com/" . $ruta;
    
            // Insertar la ruta del archivo en la tabla archivos_actividades
            $stmt = $db->prepare('INSERT INTO archivos_actividades (id_actividad, ruta_archivo) VALUES (?, ?)');
            $stmt->execute([$idActividad, $urlArchivo]);
        } else {
            throw new Exception('Error al mover el archivo ' . $nombreArchivoOriginal);
        }
    }

    // Preparar la respuesta
    $respuesta = [
        'success' => true,
        'message' => 'Actividad registrada correctamente.',
    ];
} catch (Exception $e) {
    // Manejar errores
    $respuesta = [
        'success' => false,
        'error' => 'Error al registrar la actividad: ' . $e->getMessage(),
    ];
}

// Devolver la respuesta en formato JSON
echo json_encode($respuesta);
?>