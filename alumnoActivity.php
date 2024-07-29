<?php
// Habilitar la visualización de errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el ID del grupo de la URL
    $id_grupo = isset($_REQUEST['id_grupo']) ? $_REQUEST['id_grupo'] : null;
    $idUsuario = isset($_REQUEST['id_usuario']) ? $_REQUEST['id_usuario'] : null;

    // Verificar si se proporcionó un ID de grupo
    if ($id_grupo !== null) {
        
        // Preparar la consulta
        $stmt_actividades = $conn->prepare("SELECT a.*, aa.ruta_archivo 
                                            FROM actividades a
                                            INNER JOIN actividades_grupos ag ON a.id_actividad = ag.id_actividad AND ag.id_grupo = :id_grupo
                                            LEFT JOIN archivos_actividades aa ON a.id_actividad = aa.id_actividad
                                            INNER JOIN `usuarios-grupos` ug ON ug.id_grupo = ag.id_grupo AND ug.id_usuario = :id_usuario
                                            WHERE ag.id_grupo = :id_grupo");

        // Bind the ID of the group and the user ID to the query
        $stmt_actividades->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        $stmt_actividades->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt_actividades->execute();

        // Obtener los resultados de la consulta de actividades como un array asociativo
        $actividades_usuario = $stmt_actividades->fetchAll(PDO::FETCH_ASSOC);

        // Procesar los resultados para incluir archivos de actividades en el array de actividades
        $actividades_con_archivos = array();
        foreach ($actividades_usuario as $actividad) {
            $id_actividad = $actividad['id_actividad'];
            if (!isset($actividades_con_archivos[$id_actividad])) {
                $actividades_con_archivos[$id_actividad] = $actividad;
                $actividades_con_archivos[$id_actividad]['archivos'] = array();
            }
            if ($actividad['ruta_archivo']) {
                $actividades_con_archivos[$id_actividad]['archivos'][] = $actividad['ruta_archivo'];
            }
        }

        // Convertir el array asociativo en un array numérico
        $actividades_con_archivos = array_values($actividades_con_archivos);

        // Check if activities were retrieved successfully
        if (!empty($actividades_con_archivos)) {
            // Encode the activities array into JSON format
            $response = json_encode($actividades_con_archivos);
        
            // Send the JSON response
            echo $response;
        } else {
            // If no activities were found, send an error message
            $error_message = "Error: No se encontraron actividades para el grupo $id_grupo.";
            echo "<div class='alert alert-danger' role='alert'>$error_message</div>";
        }
    } else {
        // Si no se proporcionó un ID de grupo, mostrar un mensaje de error
        $error_message = "Error: No se proporcionó un ID de grupo válido.";
        echo "<div class='alert alert-danger' role='alert'>$error_message</div>";
    }

} catch(PDOException $e) {
    // Si ocurre un error al conectar a la base de datos, mostrar un mensaje de error
    die("Error al conectar a la base de datos: ". $e->getMessage());
}
?>