<?php
// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Obtener los datos enviados desde la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibieron los datos esperados
if (isset($data->id_Grupo) && isset($data->id_Profesor)) {
    $idGrupo = $data->id_Grupo;
    $idProfesor = $data->id_Profesor;

    $sql = "SELECT a.id_actividad, a.nombre, a.fecha_entrega, a.descripcion, aa.ruta_archivo
        FROM actividades a
        INNER JOIN actividades_grupos ag ON a.id_actividad = ag.id_actividad AND ag.id_grupo = :idGrupo
        LEFT JOIN archivos_actividades aa ON a.id_actividad = aa.id_actividad
        INNER JOIN `usuarios-grupos` ug ON ug.id_grupo = :idGrupo AND ug.id_usuario = :idUsuario
        WHERE ug.id_usuario = :idUsuario";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
    $stmt->bindParam(':idUsuario', $idProfesor, PDO::PARAM_INT);
    $stmt->execute();


    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $actividades = array();

    // Obtener cada actividad y añadirla al arreglo de actividades
    foreach ($result as $row) {
        // Verificar si la actividad ya existe en el arreglo de actividades
        $actividad_existente = array_filter($actividades, function ($actividad) use ($row) {
            return $actividad['id_actividad'] == $row['id_actividad'];
        });

        // Si la actividad no existe, se añade al arreglo de actividades
        if (empty($actividad_existente)) {
            $actividad = array(
                'id_actividad' => $row['id_actividad'],
                'nombre' => $row['nombre'],
                'fecha_entrega' => $row['fecha_entrega'],
                'descripcion' => $row['descripcion'],
                'archivos' => array() // Inicializar un arreglo para los archivos de esta actividad
            );
            if (!is_null($row['ruta_archivo'])) {
                // Si hay una ruta de archivo, añadirlo al arreglo de archivos de la actividad
                $actividad['archivos'][] = $row['ruta_archivo'];
            }
            // Añadir la actividad al arreglo de actividades
            $actividades[] = $actividad;
        } else {
            // Si la actividad ya existe, añadir la ruta del archivo al arreglo de archivos de la actividad correspondiente
            $actividades[array_keys($actividad_existente)[0]]['archivos'][] = $row['ruta_archivo'];
        }
    }

    // Devolver las actividades como JSON
    header('Content-Type: application/json');
    echo json_encode($actividades);
} else {
    // Si no se recibieron los datos esperados, devolver un error
    echo "Error: Datos incompletos o incorrectos.";
}

// Cerrar la conexión con la base de datos
$db = null;
?>