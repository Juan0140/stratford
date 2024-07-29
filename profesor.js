let grupos;
    
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el indicador de inicio de sesión está presente en sessionStorage
    const sesionIniciada = sessionStorage.getItem('sesionIniciada');
    if (!sesionIniciada) {
        // Si no hay sesión iniciada, redirigir a la página de inicio de sesión
        window.location.href = 'login.html';
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        const name = urlParams.get('name');
        const id = urlParams.get('id');
        sessionStorage.setItem('name', name || ''); // Establecer el nombre en sessionStorage, si está presente en la URL
        sessionStorage.setItem('id', id || '');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Obtener el nombre de usuario almacenado en sessionStorage
    const name = sessionStorage.getItem('name');

    // Si el nombre de usuario está disponible, cambiar el texto del elemento correspondiente en el navbar
    if (name) {
        document.getElementById('nombreUsuario').textContent = name;
    }

    // Evento clic del botón "Cerrar Sesión"
    document.getElementById('cerrarSesionBtn').addEventListener('click', function() {
        // Eliminar la sesión almacenada en sessionStorage
        sessionStorage.removeItem('sesionIniciada');
        sessionStorage.removeItem('name');
        sessionStorage.removeItem('id');

        // Redirigir al usuario a la página de inicio de sesión
        window.location.href = 'login.html';
    });
    
});
    
    async function obtenerGrupos() {
        try {
            const response = await fetch('grupos.php');
    
            if (!response.ok) {
            throw new Error('Error en la solicitud');
        }
        const data = await response.json();
        return data;
      } catch (error) {
        console.error('Error:', error);
      }
    }

    obtenerGrupos().then(data => {
        grupos = data;
        cargarGruposUsuario();
    });


// Función para cargar los comentarios
    async function cargarComentarios() {
        try {
            const idGrupo = sessionStorage.getItem('id_grupo');
            const response = await fetch(`obtener_comentarios.php?id_grupo=${idGrupo}`);
            if (response.ok) {
                const comentarios = await response.json();
                mostrarComentarios(comentarios);
            } else {
                console.error('Error al obtener comentarios:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Función para mostrar los comentarios
    function mostrarComentarios(comentarios) {
        const comentariosContainer = document.getElementById('comentariosContainer');

        comentariosContainer.innerHTML = ''; // Limpiar el contenedor de comentarios antes de mostrar los nuevos comentarios
    
        comentarios.forEach(comentario => {
            // Crear un elemento div para el comentario
            const comentarioElement = document.createElement('div');
            comentarioElement.classList.add('comentario');
    
            // Crear un elemento div para el contenedor del nombre y la imagen del perfil
            const registroDiv = document.createElement('div');
            registroDiv.classList.add('registro');
    
            // Crear un elemento div para la imagen del perfil
            const imgDiv = document.createElement('div');
            imgDiv.id = 'secundario';
            const img = document.createElement('img');
            img.src = comentario.foto; // Asignar la URL de la imagen del perfil
            imgDiv.appendChild(img);
            registroDiv.appendChild(imgDiv);
    
            // Crear un elemento div para el nombre del usuario
            const principalDiv = document.createElement('div');
            principalDiv.id = 'principal';
            const h1 = document.createElement('h2');
            h1.textContent = comentario.name; // Asignar el nombre del usuario
            // Agregar el texto del comentario al comentario
            const p = document.createElement('p');
            p.textContent = comentario.texto;
            principalDiv.appendChild(h1);
            principalDiv.appendChild(p);
            
            // Verificar si el usuario loggeado es el mismo que escribió el comentario
            const idUsuarioLoggeado = sessionStorage.getItem('id');
            if (idUsuarioLoggeado === comentario.id_usuario) {
                // Si el usuario loggeado es el mismo que escribió el comentario, agregar los enlaces para borrar y editar
                const editarLink = document.createElement('a');
                editarLink.href = `editar_comentario.php?id=${comentario.id}`; // Enlace para editar el comentario
                editarLink.textContent = 'Editar';
                const borrarLink = document.createElement('a');
                borrarLink.href = '#'; // Enlace para borrar el comentario
                borrarLink.textContent = 'Borrar';
    
                // Agregar evento clic para borrar el comentario
                borrarLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Evitar comportamiento predeterminado del enlace
                    borrarComentario(comentario.id); // Llamar a la función para borrar el comentario
                });
    
                // Agregar los enlaces al comentario
                principalDiv.appendChild(editarLink);
                principalDiv.appendChild(document.createTextNode(' | ')); // Separador entre enlaces
                principalDiv.appendChild(borrarLink);
                
                // Mostrar formulario de edición
                const formularioEdicion = document.createElement('form');
                formularioEdicion.classList.add('formulario-edicion');
                formularioEdicion.action = 'actualizar_comentario.php';
                formularioEdicion.method = 'POST';
                formularioEdicion.style.display = 'none'; // Ocultar inicialmente
    
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = comentario.id;
    
                const textareaTexto = document.createElement('textarea');
                textareaTexto.name = 'texto';
                textareaTexto.textContent = comentario.texto;
    
                const botonGuardar = document.createElement('button');
                botonGuardar.type = 'submit';
                botonGuardar.textContent = 'Guardar';
                
                const botonCancelar = document.createElement('button');
                botonCancelar.type = 'cancel';
                botonCancelar.textContent = 'Cancelar';
    
                formularioEdicion.appendChild(inputId);
                formularioEdicion.appendChild(textareaTexto);
                formularioEdicion.appendChild(botonCancelar);
                formularioEdicion.appendChild(botonGuardar);
    
                principalDiv.appendChild(formularioEdicion);
    
                // Agregar evento para mostrar el formulario cuando se haga clic en "Editar"
                editarLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Evitar comportamiento predeterminado del enlace
                    formularioEdicion.style.display = 'block'; // Mostrar el formulario de edición
                });
                
                // Agregar evento clic al botón "Cancelar" del formulario de edición
                botonCancelar.addEventListener('click', function(event) {
                    event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada
                    formularioEdicion.style.display = 'none'; // Ocultar el formulario de edición
                });
                
                // Agrega el evento de envío de formulario dinámicamente dentro de la función mostrarComentarios
                formularioEdicion.addEventListener('submit', function(event) {
                    event.preventDefault(); // Evitar el envío predeterminado del formulario
                    
                    // Obtener los datos del formulario
                    const formData = new FormData(formularioEdicion);
                
                    // Realizar una solicitud AJAX a actualizar_comentario.php
                    fetch('actualizar_comentario.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Verificar si la actualización fue exitosa
                        if (data.success) {
                            // Actualizar los comentarios llamando a la función cargarComentarios()
                            cargarComentarios();
                            // Ocultar el formulario de edición
                            formularioEdicion.style.display = 'none';
                        } else {
                            // Mostrar un mensaje de error en caso de fallo
                            console.error('Error al actualizar el comentario:', data.message);
                            alert('Ocurrió un error al actualizar el comentario. Por favor, inténtalo de nuevo.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al enviar la solicitud AJAX:', error);
                        alert('Ocurrió un error al enviar la solicitud. Por favor, inténtalo de nuevo.');
                    });
                });
            }
            
            registroDiv.appendChild(principalDiv);
    
            // Agregar el contenedor del nombre y la imagen del perfil al comentario
            comentarioElement.appendChild(registroDiv);
    
            // Agregar el comentario al contenedor de comentarios
            comentariosContainer.appendChild(comentarioElement);
        });
    }
    
    // Obtener referencia al formulario de comentarios
    const comentarioForm = document.getElementById('comentarioForm');
    
    // Agregar un evento de envío al formulario de comentarios
    comentarioForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada
    
        // Obtener el valor del comentario del textarea
        const comentario = comentarioForm.querySelector('#comentario').value.trim();
    
        // Obtener el ID del usuario almacenado en sessionStorage
        const idUsuario = sessionStorage.getItem('id');
    
        // Obtener el ID del grupo almacenado en sessionStorage (o de donde sea que lo obtuviste)
        const idGrupo = sessionStorage.getItem('id_grupo');
    
        // Verificar que el comentario no esté vacío y que se hayan obtenido los IDs
        if (comentario === '' || !idUsuario || !idGrupo) {
            alert('Por favor, completa todos los campos');
            return;
        }
    
        try {
            // Realizar una solicitud POST para enviar el comentario a la base de datos
            const response = await fetch('guardar_comentario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_usuario: idUsuario, id_grupo: idGrupo, texto: comentario }),
            });
    
            if (!response.ok) {
                throw new Error('Error al enviar el comentario');
            }
    
            // Limpiar el textarea después de enviar el comentario
            comentarioForm.querySelector('#comentario').value = '';
    
            // Mostrar el nuevo comentario en la sección de comentarios
            cargarComentarios();
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error al enviar el comentario. Por favor, inténtalo de nuevo.');
        }
    });
    
    // Función para borrar un comentario
    function borrarComentario(idComentario) {
        fetch(`borrar_comentario.php?id=${idComentario}`)
        .then(response => response.json())
        .then(data => {
            // Mostrar un alert con el mensaje recibido
            alert(data.message);
            
            // Volver a cargar los comentarios
            cargarComentarios();
        })
        .catch(error => {
            console.error('Error al eliminar el comentario:', error);
            alert('Ocurrió un error al eliminar el comentario. Por favor, inténtalo de nuevo.');
        });
    }

    
    function obtenerIdGrupoActual() {
        // Aquí puedes implementar la lógica para obtener el ID del grupo actual, por ejemplo, desde el botón que abrió el modal
        const idGrupo = $('#grupoModal .btn-detalle').data('id-grupo');
        cargarComentarios();
        return idGrupo;
    }
    
    //función para cargar imagen de alumno
    const defaultFile = 'https://www.pngall.com/wp-content/uploads/5/Profile-Avatar-PNG-Picture.png';
    const file = document.getElementById('fotoAlumno');
    const img = document.getElementById('imgAlumno');
    file.addEventListener( 'change', e => {
        if(e.target.files[0]){
            const reader = new FileReader( );
            reader.onload = function( e ){
                img.src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0])
        }
        else {
            img.src = defaultFile;
        }
    })
    
    document.getElementById('cargarFotoBtn').addEventListener('click', function() {
        document.getElementById('fotoAlumno').click();
    });
    
    async function cargarAlumnosPorGrupo(idGrupo) {
    try {
        // Realizar la solicitud para obtener los alumnos del grupo utilizando el ID del grupo
        const response = await fetch('profesor-grupo.php?id_grupo=' + idGrupo, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });

        if (response.ok) {
            const alumnos = await response.json();
            mostrarAlumnosEnModal(alumnos); // Llama a la función para mostrar los alumnos en el modal del grupo
        } else {
            console.error('Error al obtener alumnos del grupo:', response.status);
        }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function mostrarAlumnosEnModal(alumnos, idGrupo) {
        const container = document.getElementById("alumnosContenedor");
    
        // Verificar si el contenedor existe
        if (!container) {
            console.error('Error: Contenedor de alumnos no encontrado.');
            return; // Salir de la función si el contenedor no existe
        }
    
        // Limpiar el contenedor
        container.innerHTML = '';
    
        if (alumnos.length > 0) {
        // Si hay alumnos, agregarlos al contenedor
        alumnos.forEach(alumno => {
            // Crear elementos HTML para el alumno
            const contenedor = document.createElement('div');
            contenedor.classList.add('registro');

            const secundario = document.createElement('div');
            secundario.id = 'alumnoFoto';

            const imagen = document.createElement('img');
            imagen.src = alumno.foto; // Utilizar la propiedad 'foto' en lugar de 'imagen'

            const principal = document.createElement('div');
            principal.id = 'alumnoData';

            const nombre = document.createElement('h3');
            nombre.textContent = alumno.name; // Utilizar la propiedad 'name' para el nombre del alumno

            const id = document.createElement('h5');
            id.textContent = 'ID: ' + alumno.id; // Utilizar la propiedad 'id' para el ID del alumno

            // Botón de eliminar
            const btnEliminar = document.createElement('button');
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.classList.add('btnEliminar');
            btnEliminar.dataset.idUsuario = alumno.id; // Almacenar el ID del usuario
            const idGrupo = sessionStorage.getItem('id_grupo');
            if (idGrupo) {
                btnEliminar.dataset.idGrupo = idGrupo; // Asignar el ID del grupo al atributo del botón
            } else {
                console.error('Error: ID del grupo no válido.');
            }
            btnEliminar.addEventListener('click', function() {
                eliminarAlumno(this.dataset.idUsuario, this.dataset.idGrupo);
            });

            // Agregar los elementos al contenedor principal
            secundario.appendChild(imagen);
            principal.appendChild(nombre);
            principal.appendChild(id);
            principal.appendChild(btnEliminar); // Agregar el botón de eliminar
            contenedor.appendChild(secundario);
            contenedor.appendChild(principal);

            // Agregar el contenedor al contenedor de alumnos
            container.appendChild(contenedor);
        });

        // Manejar el clic en la pestaña "Alumnos"
        $('#alumnos-tab').on('click', function() {
            // Quitar la clase 'active' de todas las pestañas
            $('.nav-tabs .nav-item .nav-link').removeClass('active');

            // Agregar la clase 'active' a la pestaña "Alumnos"
            $(this).addClass('active');

            // Quitar la clase 'show' de todos los paneles de pestañas
            $('.tab-content .tab-pane').removeClass('show active');

            // Agregar las clases 'show' y 'active' al panel de la pestaña de "Alumnos"
            $('#alumnosContenedor').addClass('show active');
        });
    } else {
        // Si no hay alumnos, mostrar un mensaje indicando que el grupo no tiene alumnos inscritos
        const mensaje = document.createElement('h4');
        mensaje.textContent = 'Este grupo aún no tiene alumnos inscritos.';
        container.appendChild(mensaje);

        // Manejar el clic en la pestaña "Alumnos"
        $('#alumnos-tab').on('click', function() {
            // Quitar la clase 'active' de todas las pestañas
            $('.nav-tabs .nav-item .nav-link').removeClass('active');

            // Agregar la clase 'active' a la pestaña "Alumnos"
            $(this).addClass('active');

            // Quitar la clase 'show' de todos los paneles de pestañas
            $('.tab-content .tab-pane').removeClass('show active');

            // Agregar las clases 'show' y 'active' al panel de la pestaña de "Alumnos"
            $('#alumnosContenedor').addClass('show active');
        });
    }
}

// Función para eliminar un alumno de la materia
    async function eliminarAlumno(idUsuario, idGrupo) {
        const formData = new FormData();
        formData.append('idUsuario', idUsuario);
        formData.append('idGrupo', idGrupo);
    
        const response = await fetch('quitarAlumnoGrupo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Actividad eliminada correctamente');
                // Recargar la página para actualizar la lista de actividades
                location.reload();
            } else {
                alert('Error al eliminar la actividad: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud AJAX:', error);
            alert('Error en la solicitud AJAX');
        });
    }
    
    async function cargarGruposUsuario() {
        try {
            const idUsuario = parseInt(sessionStorage.getItem('id'));
            const response = await fetch('profesor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_usuario=' + idUsuario,
            });
    
            if (response.ok) {
                const gruposUsuario = await response.json();
                if (gruposUsuario && gruposUsuario.grupos_usuario && Array.isArray(gruposUsuario.grupos_usuario)) {
                    agregarGrupos(gruposUsuario.grupos_usuario, grupos);
                    cargarAlumnosPorGrupo(gruposUsuario.grupos_usuario); // Llama a la función para cargar los alumnos por grupo
                } else {
                    console.error('Error: Datos de grupos de usuario no válidos');
                }
            } else {
                console.error('Error al obtener grupos de usuario:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Función para agregar los grupos a las columnas
    function agregarGrupos(gruposUsuario, grupos) {
        const gruposContainer = $('#gruposContainer');
        let filaActual;
    
        gruposUsuario.forEach((id_grupo, index) => {
            // Buscar el grupo correspondiente en el array 'grupos' utilizando el ID
            const grupo = grupos.find(g => g.id === id_grupo);
            if (!grupo) return; // Si no se encuentra el grupo, omitir
    
            if (index % 4 === 0) {
                filaActual = $('<div class="row mt-4"></div>'); // Crear una nueva fila para cada 4 grupos
                gruposContainer.append(filaActual); // Agregar la fila al contenedor de grupos
            }
    
            // Crear una columna para cada grupo
            const columna = $('<div class="col mb-4"></div>');
            // Crear el contenido del grupo utilizando los datos del grupo encontrado
            const contenidoGrupo = `<button class="btn btn-primary btn-detalle" data-toggle="modal" data-target="#grupoModal" data-id-grupo="${grupo.id}" data-nombre="${grupo.nombre}" data-detalles="${grupo.detalles}" data-actividades="Actividades del Grupo" data-alumnos="Alumnos del Grupo">
                <div class="card">
                    <div class="card-body">
                        <img class="icono-grupos" src="${grupo.icono}" alt="${grupo.nombre}" class="grupo-icono"> <!-- Agregar el logo del grupo -->
                    </div>
                </div>
            </button>`;
            // Agregar el contenido del grupo a la columna
            columna.append(contenidoGrupo);
            cargarCheckboxesGrupos(grupos, gruposUsuario);
            // Agregar la columna a la fila actual
            filaActual.append(columna);
    });
    
        // Manejar el clic en el botón "Ver detalles" para abrir el modal con los detalles del grupo
        $('.btn-detalle').on('click', function() {
            const nombre = $(this).data('nombre');
            const detalles = $(this).data('detalles');
            const actividades = $(this).data('actividades');
            const alumnos = $(this).data('alumnos');
    
            // Actualizar el contenido del modal con los detalles del grupo
            $('#grupoModal .modal-title').text(nombre);
            $('#detallesTab').text(detalles);
            $('#alumnosTab').text(alumnos);
    
            // Obtener el ID del grupo desde el botón
            const idGrupo = $(this).data('id-grupo');
            sessionStorage.setItem('id_grupo', idGrupo);
    
            // Llamar a la función para cargar los alumnos por grupo
            cargarAlumnosPorGrupo(idGrupo);
        });
    }

    // Función para cargar dinámicamente los checkboxes de los grupos disponibles
    function cargarCheckboxesGrupos(grupos, idGrupo) {
        const checkboxesContainer = document.getElementById("checkboxesContainer");
        if (!checkboxesContainer) {
            console.error('Error: Contenedor de checkboxes no encontrado.');
            return;
        }
        checkboxesContainer.innerHTML = ''; // Limpiar el contenedor
    
        grupos.forEach(grupo => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.classList.add('form-check');
    
            const checkboxInput = document.createElement('input');
            checkboxInput.type = 'checkbox';
            checkboxInput.classList.add('form-check-input');
            checkboxInput.id = `grupo${grupo.id}`;
            checkboxInput.name = 'grupos[]'; // Este nombre es importante para que los datos se envíen como un array
            checkboxInput.value = grupo.id; // Establecer el valor del checkbox como el ID del grupo
    
            // Si el ID del grupo coincide con el ID del grupo seleccionado, marcar el checkbox como seleccionado
            if (grupo.id === idGrupo) {
                checkboxInput.checked = true;
            }
    
            const checkboxLabel = document.createElement('label');
            checkboxLabel.classList.add('form-check-label');
            checkboxLabel.setAttribute('for', `grupo${grupo.id}`);
            checkboxLabel.textContent = grupo.nombre; // Establecer el texto del checkbox como el nombre del grupo
    
            checkboxDiv.appendChild(checkboxInput);
            checkboxDiv.appendChild(checkboxLabel);
    
            checkboxesContainer.appendChild(checkboxDiv);
        });
    }
    
    // Función para cargar las actividades del grupo y del profesor en la pestaña de actividades del modal
    async function cargarActividades() {
        try {
            const idGrupo = sessionStorage.getItem('id_grupo');
            const idProfesor = sessionStorage.getItem('id');
            
            // Realizar una solicitud al servidor para obtener las actividades del grupo y del profesor
            const response = await fetch('obtener_actividades.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_Grupo: idGrupo, id_Profesor: idProfesor }),
            });
    
            if (response.ok) {
                const actividades = await response.json();
                if (actividades.length > 0) {
                    // Si hay actividades, mostrarlas
                    mostrarActividades(actividades);
                } else {
                    // Si no hay actividades, mostrar el mensaje correspondiente
                    mostrarActividades([]);
                }
            } else {
                console.error('Error al obtener actividades:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    let idActividadEliminar;
    
    function mostrarActividades(actividades) {
        const actividadesContainer = $('#actividades .align-space-evenly-grid');
        actividadesContainer.empty(); // Vaciar el contenedor de actividades
    
        if (actividades.length > 0) {
            // Si hay actividades, agregarlas al contenedor
            actividades.forEach((actividad, index) => {
                // **Solución:** Definir la variable `actividad` dentro del bucle
                const contenidoActividad = `
                    <div class="my-cell">
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn botn-link" data-toggle="collapse" data-target="#activity-${actividad.id_actividad}" aria-expanded="false">${actividad.nombre}</button>
                            <div class="btn-group" role="group">
                                <button class="eliminar-actividad show btn-danger text-white" href="#" data-id-actividad="${actividad.id_actividad}">Eliminar</button>
                            </div>
                        </div>
                        <div id="activity-${actividad.id_actividad}" class="collapse">
                            <p>Fecha Entrega: ${actividad.fecha_entrega}</p>
                            <p>Descripción: ${actividad.descripcion}</p>
                            <div><p>Archivos:</p></div>
                            <ul>
                                ${actividad.archivos.map((archivo, idx) => `<li style="color:white"><a href="${archivo}" target="_blank" class="enlace-archivo">Archivo ${idx + 1}</a></li>`).join('')}
                            </ul>
                        </div>
                    </div>`;
                actividadesContainer.append(contenidoActividad);
            });
        } else {
            // Si no hay actividades, mostrar un mensaje indicando que no hay actividades registradas
            actividadesContainer.html('<h4>No hay actividades registradas para este grupo y profesor.</h4>');
        }
    }
    
    // Manejador de eventos para el botón de eliminación
    $(document).on('click', '.eliminar-actividad', function() {
        const idActividad = $(this).data('id-actividad');
    
        // Abre el modal de confirmación de eliminación y guarda el ID de la actividad a eliminar
        $('#confirmacionEliminarModal').modal('show');
        idActividadEliminar = idActividad;
    });
    
    // Envío de la solicitud AJAX para eliminar la actividad
    $(document).on('click', '.btn-eliminar-actividad', function() {
        // Enviar la solicitud AJAX para eliminar la actividad
        fetch('profeBorrarActividad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_actividad=' + idActividadEliminar,
        })
       .then(response => response.json())
       .then(data => {
            if (data.success) {
                alert('Actividad eliminada correctamente');
                // Recargar la página para actualizar la lista de actividades
                location.reload();
            } else {
                alert('Error al eliminar la actividad: ' + data.error);
            }
        })
       .catch(error => {
            console.error('Error en la solicitud AJAX:', error);
            alert('Error en la solicitud AJAX');
        });
    });
    
    // Llamar a la función para cargar las actividades cuando se abre el modal
    $('#grupoModal').on('show.bs.modal', function (event) {
        cargarActividades();
        cargarComentarios();
    });
    
    // Envío del formulario de actividad
    document.querySelector('.submit-button').addEventListener('click', function(e) {
      e.preventDefault();
    
      const form = document.querySelector('#activityForm');
      const formData = new FormData(form);
      // Agregar las claves faltantes al objeto FormData
      formData.append('nombre', $('#activityName').val());
      formData.append('fecha_entrega', $('#dueDate').val());
      formData.append('descripcion', $('#description').val());
      formData.append('archivo', $('#documento')[0].files[0]);
    
      // Obtener el idGrupo desde sessionStorage
      const idGrupo = sessionStorage.getItem('id_grupo');
      // Agregar el idGrupo al objeto FormData
      formData.append('id_grupo', idGrupo);
    
      console.log('Llamando a actividades-reg.php');
      fetch('actividades-reg.php', {
        method: 'POST',
        body: formData
      })
     .then(response => response.json())
     .then(data => {
        if (data.success) {
          alert('Actividad registrada correctamente');
        } else {
          alert('Error al registrar la actividad: ' + data.error);
        }
      })
     .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        alert('Error en la solicitud AJAX');
      });
    });

// Evento clic del botón "Asignar"
$('.btn-enviar-actividad').on('click', async function() {
    // Obtener el id_grupo del botón de detalle del grupo
    const idGrupo = $('#grupoModal .btn-detalle').data('id-grupo');
    // Guardar el id_grupo en sessionStorage
    sessionStorage.setItem('id_grupo', idGrupo);
});

      // Event for button click to load files (if implemented)
      $('#fileInputBtn').on('click', function() {
        // Logic for opening file selection dialog (replace with your implementation)
        $('#input-file').click();
      });
    
      // Optional event for input change (for different behavior)
      $('#input-file').on('change', function() {
        // Handle file selection from input (if needed)
        const files = $(this)[0].files;
        handleFiles(files);
});