<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['texto'])) {
    // Obtener los datos del formulario
    $idComentario = $_POST['id'];
    $texto = $_POST['texto'];

    // Conectar a la base de datos (reemplaza los valores con los de tu conexión)
    $servername = "srv901.hstgr.io";
    $basededatos = "u370632749_Stratford";
    $username = "u370632749_Stratford";
    $password = "=IvlK|?:ei/0";

    $conn = new mysqli($servername, $username, $password, $basededatos);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }
    
    if (isset($_POST['id']) && isset($_POST['texto'])) {
      $idComentario = $_POST['id'];
      $textoEditado = $_POST['texto'];
    
      // Update the comment text in the database
      $sql = "UPDATE Comentarios SET texto = ? WHERE id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("si", $textoEditado, $idComentario);
    
      if ($stmt->execute()) {
        // Send a JSON response indicating success
        echo json_encode(['success' => true]);
      } else {
        // Send a JSON response indicating error with message
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el comentario']);
      }
    } else {
      // Send a JSON response indicating missing data
      echo json_encode(['success' => false, 'message' => 'Datos del formulario incompletos']);
    }
    
    // Close the database connection
    $conn->close();
} else {
    // Si no se recibieron los datos del formulario, redirigir al usuario de vuelta a la página anterior y mostrar un mensaje de error
    echo "<script>alert('No se recibieron los datos del formulario'); window.history.back();</script>";
}
?>