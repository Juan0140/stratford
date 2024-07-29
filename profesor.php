<?php
$servername = "srv901.hstgr.io";
$basededatos = "u370632749_Stratford";
$username = "u370632749_Stratford";
$password = "=IvlK|?:ei/0";

// Recibe el ID de usuario y el archivo del cuerpo de la solicitud
$id_usuario = $_POST['id_usuario'];
$file = $_FILES['files'];

try {
  $conn = new PDO("mysql:host=$servername;dbname=$basededatos", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Consulta SQL para obtener los grupos asociados al usuario
  $stmt = $conn->prepare("SELECT id_grupo FROM `usuarios-grupos` WHERE id_usuario = :id_usuario");
  $stmt->bindParam(':id_usuario', $id_usuario);
  $stmt->execute();

  // Obtiene los resultados de la consulta como un array
  $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Prepara un array para almacenar solo los IDs de los grupos
  $grupos_usuario = array_column($resultados, 'id_grupo');

  // Consulta SQL para obtener las actividades asociadas a los grupos del usuario
  $stmt_actividades = $conn->prepare("SELECT a.* FROM actividades a INNER JOIN actividades_grupos ag ON a.id_actividad = ag.id_actividad WHERE ag.id_grupo IN (" . implode(",", $grupos_usuario) . ")");
  $stmt_actividades->execute();

  // Obtiene los resultados de la consulta de actividades como un array
  $actividades_usuario = $stmt_actividades->fetchAll(PDO::FETCH_ASSOC);

  // Validar el archivo
  if ($file['error'] === 0) {
    // Guardar el archivo
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $target_file);

    // Agregar información del archivo a la respuesta
    $data['archivo'] = array(
      'nombre' => $file['name'],
      'ruta' => $target_file,
      'fecha_subida' => date('Y-m-d H:i:s')
    );
  } else {
    // Manejar el error de subida
    $data['error_archivo'] = 'Error al subir el archivo.';
  }

  // Prepara los datos para ser devueltos como respuesta JSON
  $data = array(
    'grupos_usuario' => $grupos_usuario,
    'actividades_usuario' => $actividades_usuario,
  );

  // Devuelve los datos como una respuesta JSON
  echo json_encode($data);
} catch(PDOException $e) {
    // Si ocurre un error durante la ejecución de la consulta SQL, devuelve un mensaje de error
    http_response_code(500);
    echo json_encode(array('error' => 'Error: ' . $e->getMessage()));
}
?>