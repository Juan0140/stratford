<?php
require 'includes/db/db.php';
require 'includes/functions/funciones.php';
session_start();

$profesores = getProfesores($conn);
$horarios = getHorarios($conn);
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Horarios</title>
    <link rel="stylesheet" href="build/css/index.css">
    <link rel="stylesheet" href="build/css/normalize.css">
    <script src="build/js/nav.js"></script>
    <script src="build/js/horarios.js"></script>
</head>

<body>
    <header>
        <nav class="navegador">
            <div class="contenido-nav">
                <div class="imagen-nav">
                    <img src="https://stratfordlernen.com/images/de1a56e713384c6ad4a7bba0d040b881.svg" alt="Logo">
                    <svg id="toggle-nav" class="toggle-nav" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 6l16 0" />
                        <path d="M4 12l16 0" />
                        <path d="M4 18l16 0" />
                    </svg>
                </div>
            </div>
        </nav>
    </header>
    <main class="contenedor">
        <div class="filtro">
            <label for="filtro-dia">Filtro por día</label>
            <div class="select-filtro">
                <select name="filtro-dia" id="filtro-dia">
                    <option value="0" selected>Ninguno</option>
                    <?php
                    $j = 1;
                    foreach ($dias as $dia) : ?>
                        <option value="<?php echo $j ?>"><?php echo $dia ?></option>
                    <?php
                        $j++;
                    endforeach;
                    ?>
                </select>
            </div>
        </div>

        <?php
        $i = 1;
        foreach ($dias as $dia) : ?>
            <div class="horario-dia" data-dia="<?php echo $i; ?>">
                <div class="enca-tabla">
                    <h2><?php echo $dia; ?></h2>
                </div>
                <table border="0" class="tabla">
                    <tr class="thead">
                        <?php foreach ($profesores as $id => $nombre) : ?>
                            <th><?php echo $nombre; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                    $horas = [];
                    foreach ($horarios[$dia] as $horario) {
                        $horas[$horario['hora']][] = $horario;
                    }
                    foreach ($horas as $hora => $detalles) :
                    ?>
                        <tr class="tbody">
                            <?php foreach ($profesores as $id => $nombre) : ?>
                                <td>
                                    <?php
                                    $found = false;
                                    foreach ($detalles as $detalle) {
                                        if ($detalle['idProfesor'] == $id) {
                                            $found = true;
                                            $hora_12 = date("h:i A", strtotime($detalle['hora']));
                                    ?>
                                            <div class="contenido-tabla">
                                                <p><?php echo $detalle['materia'] ?></p>
                                                <p>(<?php echo $detalle['modalidad'] ?>)</p>
                                                <p><?php echo $hora_12 ?></p>
                                            </div>
                                    <?php
                                            break;
                                        }
                                    }
                                    if (!$found) {
                                        echo "&nbsp;";
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <br>
            </div>
        <?php
            $i++;
        endforeach;
        ?>
    </main>
    <?php $conn->close() ?>
</body>

</html>
