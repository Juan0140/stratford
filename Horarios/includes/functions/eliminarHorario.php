<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id= $_POST['idHorario'];
    $query="DELETE FROM horarios WHERE id = '{$id}'";
    $result = $conn->query($query);
    if($result){
        $conn->close();
        $_SESSION['alerta']=1;
        $_SESSION['mensaje']="El horario se ha eliminado";
        header("Location: ../../horariosAdmin.php");
    }
}