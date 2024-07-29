<?php

function getProfesores($conn)
{
    $sql = "SELECT id, nombre FROM profesores";
    $result_profesores = $conn->query($sql);
    $profesores = [];
    if ($result_profesores->num_rows > 0) {
        while ($row = $result_profesores->fetch_assoc()) {
            $profesores[$row['id']] = $row['nombre'];
        }
    }
    return $profesores;
}

function getHorarios($conn)
{
    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $horarios = [];
    foreach ($dias as $dia) {
        $sql = "SELECT id, idProfesor, materia, hora, modalidad FROM horarios WHERE dia = '$dia' ORDER BY hora ASC";
        $result_horarios = $conn->query($sql);
        $horarios[$dia] = [];
        if ($result_horarios->num_rows > 0) {
            while ($row = $result_horarios->fetch_assoc()) {
                $horarios[$dia][] = $row;
            }
        }
    }
    return $horarios;
}

function debuguear($var)
{
    echo ("<pre>");
    var_dump($var);
    echo ("</pre>");
    exit;
}
