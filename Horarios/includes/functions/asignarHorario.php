<?php
require '../db/db.php';
require 'funciones.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isset($_POST['idProfesor']) || !isset($_POST['dia']) || $_POST['hora']=="") {
        $_SESSION['form_data'] = $_POST;
        sendAlert($conn, 3, 'LLena todos los campos', '../../horariosAdmin.php');
        return;
    }
    $idProfesor = $_POST['idProfesor'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $dia = $conn->real_escape_string($dia);
    $idProfesor = $conn->real_escape_string($idProfesor);
    $hora = $conn->real_escape_string($hora);

    $query = "SELECT id, materia, modalidad FROM horarios WHERE idProfesor = '{$idProfesor}' AND dia = '{$dia}' AND hora = '{$hora}'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $resultado = $result->fetch_assoc();
        $id = $resultado['id'];
        $materia = $_POST['materia']=="" ? $resultado['materia'] : $_POST['materia'];
        $modalidad = isset($_POST['modalidad'])? $_POST['modalidad'] : $resultado['modalidad'];
        $materia = $conn->real_escape_string($materia);

        $query1 = "UPDATE horarios set materia = '{$materia}', modalidad = '{$modalidad}' WHERE id = '{$id}'";
        $result1 = $conn->query($query1);
        if ($result1) {
            $conn->close();
            $_SESSION['alerta'] = 1;
            $_SESSION['mensaje'] = "El horario se ha asignado";
            header("Location: ../../horariosAdmin.php");
        }
    } else {
        if($_POST['materia']=="" || !isset($_POST['modalidad'])) {
            $_SESSION['form_data'] = $_POST;
            sendAlert($conn, 3, 'LLena todos los campos', '../../horariosAdmin.php');
            return;
        }
        $modalidad =$_POST['modalidad'];
        $materia = $_POST['materia'];
        $materia = $conn->real_escape_string($materia);
        $modalidad = $conn->real_escape_string($modalidad);
        $query1 = "INSERT INTO horarios (idProfesor, materia, dia, hora, modalidad) VALUES ('{$idProfesor}', '{$materia}', '{$dia}', '{$hora}', '{$modalidad}')";
        $result1 = $conn->query($query1);
        if ($result1) {
            $conn->close();
            $_SESSION['alerta'] = 1;
            $_SESSION['mensaje'] = "El horario se ha asignado";
            header("Location: ../../horariosAdmin.php");
        }
    }
}
