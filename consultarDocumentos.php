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

// Función para consultar los documentos del alumno
function consultarDocumentos($id_alumno, $id_actividad) {
    $conn = conectarDB();
    try {
        // Consultar la tabla Documentos para obtener los documentos del alumno
        $consulta = "SELECT * FROM Documentos WHERE id_alumno = :id_alumno AND id_actividad = :id_actividad";
        $stmt = $conn->prepare($consulta);
        $stmt->bindParam(':id_alumno', $id_alumno);
        $stmt->bindParam(':id_actividad', $id_actividad);
        $stmt->execute();
        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $documentos;
    } catch(PDOException $e) {
        // Manejar errores de consulta
        die("Error al consultar documentos: ". $e->getMessage());
    }
}

$id_alumno = $_GET['id_alumno'];
$id_actividad = $_GET['id_actividad'];
$documentos = consultarDocumentos($id_alumno, $id_actividad);

// Devolver los documentos como respuesta JSON
header('Content-Type: application/json');
echo json_encode($documentos);
?>