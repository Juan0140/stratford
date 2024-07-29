<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $nombre = $conn->real_escape_string($nombre);
    $query = "INSERT INTO profesores (nombre) VALUES ('{$nombre}')";
    $result = $conn->query($query);
    if($result) {
        $conn->close();
        $_SESSION['alerta']=1;
        $_SESSION['mensaje']="El profesor se ha agregado";
        header("Location: ../../profesoresAdmin.php");
    }
}