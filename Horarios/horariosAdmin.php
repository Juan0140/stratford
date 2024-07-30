<?php
require 'includes/db/db.php';
require 'includes/functions/funciones.php';
session_start();
isAuth();

$profesores = getProfesores($conn);
$horarios = getHorarios($conn);
$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
$form_data = [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
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
    <script src="build/js/alerta.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    if (isset($_SESSION['alerta'])) {
        if ($_SESSION['alerta'] == '1') { ?>
            <script>
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "<?php echo $_SESSION['mensaje'] ?>",
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>
    <?php
            unset($_SESSION['alerta']);
            unset($_SESSION['mensaje']);
        }
    }
    ?>
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
                    <a href="./horariosAdmin.php" class="hiper active">Horarios</a>
                    <a href="./profesoresAdmin.php" class="hiper">Profesores</a>
                    <a href="includes/functions/cerrarSesion.php" class="hiper">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="contenedor">
        <p>¡Hola!, <?php echo $_SESSION['name'] ?>.</p>
        <?php
        if (isset($_SESSION['alerta']) && $_SESSION['alerta'] == 3) { ?>
            <div class="alerta" id="alerta">
                <p><?php echo $_SESSION['mensaje']; ?></p>
            </div>
        <?php 
            unset($_SESSION['alerta']);
            unset($_SESSION['mensaje']);
        }
        ?>
        <form action="includes/functions/asignarHorario.php" method="post">
            <fieldset class="formulario-in">
                <legend>Insertar o Actualizar Horario</legend>
                <div class="campo">
                    <label for="profesor">Profesor</label>
                    <select name="idProfesor" id="profesor">
                        <option value=" " disabled selected>--SELECCIONA UN PROFESOR--</option>
                        <?php foreach ($profesores as $id => $nombre) : ?>
                            <option value="<?php echo $id ?>" <?php echo isset($form_data['idProfesor']) && $form_data['idProfesor'] == $id ? 'selected' : ''; ?>>
                                <?php echo $nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="dia">Día</label>
                    <select name="dia" id="dia">
                        <option value="" disabled selected>--SELECCIONA UN DÍA--</option>
                        <?php foreach ($dias as $dia) : ?>
                            <option value="<?php echo $dia ?>" <?php echo isset($form_data['dia']) && $form_data['dia'] == $dia ? 'selected' : ''; ?>>
                                <?php echo $dia ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="hora">Hora</label>
                    <input type="time" id="hora" name="hora" value="<?php echo isset($form_data['hora']) ? $form_data['hora'] : ''; ?>">
                </div>
                <div class="campo">
                    <label for="materia">Materia</label>
                    <input type="text" name="materia" id="materia" placeholder="Escribe la materia" value="<?php echo isset($form_data['materia']) ? $form_data['materia'] : ''; ?>">
                </div>
                <div class="campo ">
                    <label for="">Modalidad</label>
                    <div class="radios">
                        <label for="presencial">
                            <input type="radio" name="modalidad" value="Presencial" id="presencial" <?php echo isset($form_data['modalidad']) && $form_data['modalidad'] == 'Presencial' ? 'checked' : ''; ?>>Presencial
                        </label>
                        <label for="linea">
                            <input type="radio" name="modalidad" value="En Linea" id="linea" <?php echo isset($form_data['modalidad']) && $form_data['modalidad'] == 'En Linea' ? 'checked' : ''; ?>>En línea
                        </label>
                    </div>
                </div>
                <div class="campo btn-form">
                    <input type="submit" class="boton btn-form" value="Asignar">
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
                    <form class="form-vaciar" method="POST" action="includes/functions/vaciarDia.php">
                        <input type="hidden" name="dia" value="<?php echo $dia ?>">
                        <input type="submit" value="Vaciar día" class="boton-eliminar">
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
                                            $hora_12 = date("h:i A", strtotime($detalle['hora']));
                                    ?>
                                            <div class="contenido-tabla">
                                                <p><?php echo $detalle['materia'] ?></p>
                                                <p>(<?php echo $detalle['modalidad'] ?>)</p>
                                                <p><?php echo $hora_12 ?></p>
                                                <form method="POST" action="includes/functions/eliminarHorario.php" class="form-eliminar">
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

    <script>
        function confirmarEliminacion(event) {
            event.preventDefault(); // Previene el envío inmediato del formulario

            Swal.fire({
                title: '¿Estás seguro?',
                html: '<p class="text-alert">No podrás revertir esto</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'custom-button',
                    cancelButton: 'custom-button',
                    htmlContainer: 'custom-container',
                    icon: 'icono',
                    title: 'titulo-alert'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit(); // Enviar el formulario si se confirma
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-eliminar').forEach(function(form) {
                form.addEventListener('submit', confirmarEliminacion);
            });

            // Añadir el evento de confirmación a todos los formularios con la clase form-vaciar
            document.querySelectorAll('.form-vaciar').forEach(function(form) {
                form.addEventListener('submit', confirmarEliminacion);
            });
        });
    </script>
    <?php $conn->close() ?>
</body>

</html>