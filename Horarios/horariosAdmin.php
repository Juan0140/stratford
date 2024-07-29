<?php
require 'includes/db/db.php';
require 'includes/functions/funciones.php';

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
                <div class="hiper-nav">
                    <a href="" class="hiper active">Horarios</a>
                    <a href="" class="hiper">Profesores</a>
                    <a href="" class="hiper">Cambiar Contraseña</a>
                    <a href="" class="hiper">Cerrar Sesion</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="contenedor">
        <form action="">
            <fieldset class="formulario-in">
                <legend>Insertar o Actualizar Horario</legend>
                <div class="campo">
                    <label for="">Profesor</label>
                    <select name="" id="">
                        <option value="" selected disabled>--SELECCIONA UN PROFESOR</option>
                        <option value="">Juan</option>
                        <option value="">Pedro</option>
                    </select>
                </div>
                <div class="campo">
                    <label for="">Dia</label>
                    <select name="" id="">
                        <option value="" selected disabled>--SELECCIONA UN DIA</option>
                        <option value="">Juan</option>
                        <option value="">Pedro</option>
                    </select>
                </div>
                <div class="campo">
                    <label for="">Hora</label>
                    <input type="time">
                </div>
                <div class="campo">
                    <label for="">Materia</label>
                    <input type="text" name="" id="">
                </div>
                <div class="campo ">
                    <label for="">Modalidad</label>
                    <div class="radios">
                        <label for="presencial">
                            <input type="radio" name="modalidad" value="presencial" id="presencial">Presencial
                        </label>
                        <label for="linea">
                            <input type="radio" name="modalidad" value="linea" id="linea">En linea
                        </label>
                    </div>
                </div>
                <div class="campo btn-form">
                    <input type="submit" class="boton btn-form">
                </div>
            </fieldset>
        </form>

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
                    <form>
                        <input type="hidden" value="<?php echo $dia ?>">
                        <input type="submit" value="Vaciar dia" class="boton-eliminar">
                    </form>
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
                                    ?>
                                            <div class="contenido-tabla">
                                                <p><?php echo $detalle['materia'] ?></p>
                                                <p><?php echo $detalle['modalidad'] ?></p>
                                                <p><?php echo $detalle['hora'] ?></p>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="idHorario" value="<?php echo $detalle['id']; ?>">
                                                    <input type="submit" name="eliminar" value="Eliminar" class="boton-eliminar">
                                                </form>
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
</body>

</html>