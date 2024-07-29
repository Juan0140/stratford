<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dia= $_POST['dia'];
    $query="DELETE FROM horarios WHERE dia = '{$dia}'";
    $result = $conn->query($query);
    if($result){
        $conn->close();
        $_SESSION['alerta']=1;
        $_SESSION['mensaje']="El horario se ha eliminado";
        header("Location: ../../horariosAdmin.php");
    }
}