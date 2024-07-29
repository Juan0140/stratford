<?php
// Conexión a la base de datos
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

$conn = new mysqli($servername, $username, $password, $basededatos);

// Determinar el valor de la columna 'profesor' basado en el valor del campo 'tipo'
$profesor = ($tipo == 'docente') ? 1 : 0;

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consulta para obtener los profesores
$sql_profesores = "SELECT id, name, user, foto FROM login WHERE profesor = 1";
$result_profesores = $conn->query($sql_profesores);

$profesores = array();

if ($result_profesores->num_rows > 0) {
    // Convertir los resultados en un array asociativo
    while ($row = $result_profesores->fetch_assoc()) {
        $profesores[] = $row;
    }
}

// Consulta para obtener los alumnos
$sql_alumnos = "SELECT id, name, user, foto FROM login WHERE admin = 0 AND profesor = 0";
$result_alumnos = $conn->query($sql_alumnos);

$alumnos = array();

if ($result_alumnos->num_rows > 0) {
    // Convertir los resultados en un array asociativo
    while ($row = $result_alumnos->fetch_assoc()) {
        $alumnos[] = $row;
    }
}

$sql_grupos = "SELECT * FROM grupos";
$result_grupos = $conn->query($sql_grupos);

$grupos = array();

if ($result_grupos->num_rows > 0) {
    // Convertir los resultados en un array asociativo
    while ($row = $result_grupos->fetch_assoc()) {
        $grupos[] = $row;
    }
}

// Crear un array asociativo que contenga ambas listas de profesores y alumnos
$datos = array(
    "profesores" => $profesores,
    "alumnos" => $alumnos,
    "grupos" => $grupos
);

// Devolver los datos en formato JSON
echo json_encode($datos);

$conn->close();
?>