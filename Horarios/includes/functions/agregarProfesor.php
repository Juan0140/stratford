<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['nombre']==""){
        sendAlert($conn, 3, "Escribe el nombre del profesor","../../profesoresAdmin.php");
    }
    $nombre = $_POST['nombre'];
    $nombre = $conn->real_escape_string($nombre);
    $query1 = "SELECT * FROM profesores WHERE nombre = '{$nombre}'";
    $result1 = $conn->query($query1);
    if($result1->num_rows > 0) {
        $conn->close();
        $_SESSION['alerta']=2;
        $_SESSION['mensaje']="El profesor ya existe";
        header("Location: ../../profesoresAdmin.php");
    }
    $query = "INSERT INTO profesores (nombre) VALUES ('{$nombre}')";
    $result = $conn->query($query);
    if($result) {
        $conn->close();
        $_SESSION['alerta']=1;
        $_SESSION['mensaje']="El profesor se ha agregado";
        header("Location: ../../profesoresAdmin.php");
    }
}