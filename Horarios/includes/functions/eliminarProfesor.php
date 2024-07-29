<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['idProfesor'];
    $query="DELETE FROM horarios WHERE idProfesor = '{$id}'";
    $query1="DELETE FROM profesores WHERE id = '{$id}'";
    $result =  $conn->query($query);
    if($result) {
        $result1 = $conn->query($query1);
        if($result1) {
            $conn->close();
            $_SESSION['alerta']=1;
            $_SESSION['mensaje']="El profesor se ha eliminado";
            header("Location: ../../profesoresAdmin.php");
        }
    }
}