<?php
require 'includes/db/db.php';
require 'includes/functions/funciones.php';
session_start();

$profesores = getProfesores($conn);
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
    <script src="build/js/profesores.js"></script>
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
    <?php  }
        unset($_SESSION['alerta']);
        unset($_SESSION['mensaje']);
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
                    <a href="./horariosAdmin.php" class="hiper">Horarios</a>
                    <a href="./profesoresAdmin.php" class="hiper active">Profesores</a>
                    <a href="" class="hiper">Cambiar Contraseña</a>
                    <a href="" class="hiper">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="contenedor-small">
        <form action="includes/functions/agregarProfesor.php" method="post">
            <fieldset class="formulario-in m-r">
                <legend>Agregar Profesor</legend>
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ingresa el nombre de profesor" required>
                </div>
                <div class="campo btn-form">
                    <input type="submit" value="Agregar" class="boton">
                </div>
            </fieldset>
        </form>

        <table>
            <tr>
                <th>Profesor</th>
                <th>Acciones</th>
            </tr>
            <?php
            foreach ($profesores as $id => $nombre) : ?>
                <tr>
                    <td><?php echo $nombre ?></td>
                    <td>

                        <form method="POST" action="includes/functions/eliminarProfesor.php" class="form-eliminar">
                            <div class="acciones">
                                <input type="hidden" name="idProfesor" value="<?php echo $id ?>">
                                <input type="submit" name="eliminar" value="Eliminar" class="boton-eliminar">
                            </div>
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
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
</body>

</html>