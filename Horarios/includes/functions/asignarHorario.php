<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idProfesor = $_POST['idProfesor'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $materia = $_POST['materia'];
    $modalidad = $_POST['modalidad'];

    //? Escapamos los atributos
    $materia = $conn->real_escape_string($materia);
    $hora = $conn->real_escape_string($hora);

    $query = "SELECT id FROM horarios WHERE idProfesor = '{$idProfesor}' AND dia = '{$dia}' AND hora = '{$hora}'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $resultado = $result->fetch_assoc();
        $id = $resultado['id'];
        $query1 = "UPDATE horarios set materia = '{$materia}', modalidad = '{$modalidad}' WHERE id = '{$id}'";
        $result1 = $conn->query($query1);
        if($result1){
            $conn->close();
            $_SESSION['alerta']=1;
            $_SESSION['mensaje']="El horario se ha asignado";
            header("Location: ../../horariosAdmin.php");
        }
    }else{
        $query1 = "INSERT INTO horarios (idProfesor, materia, dia, hora, modalidad) VALUES ('{$idProfesor}','{$materia}',
        '{$dia}', '{$hora}', '{$modalidad}')";
        $result1 = $conn->query($query1);
        if($result1) {
            $conn->close();
            $_SESSION['alerta']=1;
            $_SESSION['mensaje']="El horario se ha asignado";
            header("Location: ../../horariosAdmin.php"); 
        }
    }
}
