let grupos;

document.addEventListener('DOMContentLoaded', function() {
    const sesionIniciada = sessionStorage.getItem('sesionIniciada');
    if (!sesionIniciada) {
        window.location.href = 'login.html';
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        const name = urlParams.get('name');
        const id = urlParams.get('id');
        sessionStorage.setItem('name', name || '');
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
    grupos = data; // Asigna los grupos obtenidos a la variable 'grupos'
    
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    fetch('alumno.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_usuario=' + id,
    })
        .then(response => response.json())
        .then(data => {
            // Show student's groups in the modal
            const gruposContainer = document.getElementById('gruposContainer');
            data.grupos_usuario.forEach(grupoId => {
                const grupo = grupos.find(g => g.id === grupoId);

                if (grupo) {
                    const grupoElement = document.createElement('div');
                    grupoElement.innerHTML = `
                      <button class="btn btn-primary btn-detalle" data-toggle="modal" data-target="#grupoModal" data-id-grupo="${grupo.id}" data-nombre="${grupo.nombre}" data-detalles="${grupo.detalles}" data-actividades="${grupo.actividades}" data-alumnos="${grupo.alumnos}">
                        <div class="card">
                          <div class="card-body">
                            <img class="icono-grupos" src="${grupo.icono}" alt="${grupo.nombre}" class="grupo-icono">
                          </div>
                        </div>
                      </button>`;
                    gruposContainer.appendChild(grupoElement);
                }
            });
        });
});

    document.addEventListener('DOMContentLoaded', function() {
        const gruposContainer = document.getElementById('gruposContainer');
    
        // Delegate event to avoid binding click events to every newly added button
        gruposContainer.addEventListener('click', function(event) {
            const target = event.target;
    
            // Check if clicked element has class 'btn-detalle'
            if (target.classList.contains('btn-detalle')) {
                const idGrupo = target.getAttribute('data-id-grupo');  // Ensure correct spelling
    
                console.log('Cargando actividades para el grupo', idGrupo);
    
                // Handle asynchronous operations if `cargarActividades` makes them:
                if (typeof cargarActividades === 'function') {
                    cargarActividades(idGrupo).then(() => {
                        // Code to execute after activities are loaded (optional)
                    });
                } else {
                    console.warn('cargarActividades function not found or not a function');
                }
            }
        });
    });
    
    function agregarGrupos(gruposUsuario, grupos) {
        // Declarar la variable `actividadesGrupo` dentro de la función
        const actividadesGrupo = [];
        gruposUsuario.forEach(grupoId => {
            const grupo = grupos.find(g => g.id === grupoId);
            if (grupo) {
                actividadesContainer.innerHTML = '';
                // Filtrar las actividades por grupo
                const actividadesGrupo = actividadesUsuario.filter(actividad => actividad.id_grupo == grupoId);
            }
        });

        if (actividadesGrupo.length === 0) {
            actividadesContainer.innerHTML = '';
            const noActividadesElement = document.createElement('h4');
            noActividadesElement.textContent = 'No hay actividades registradas';
            actividadesContainer.appendChild(noActividadesElement);
        } else {
            actividadesGrupo.forEach(actividad => {
                const actividadElement = document.createElement('div');
                actividadElement.textContent = actividad.nombre;
                actividadesContainer.appendChild(actividadElement);
            });
        }
    }
    
    async function cargarActividades(idGrupo) {
        try {
            // Almacena el ID del grupo en sessionStorage
            sessionStorage.setItem('id_grupo', idGrupo);
            var idUsuario = sessionStorage.getItem('id');
            
            if (!idUsuario) {
                console.error('No se encontró el idUsuario en el Session Storage.');
                return;
            }
    
            // Realizar una solicitud AJAX al archivo adminActivity.php
            const response = await fetch(`alumnoActivity.php?id_grupo=${idGrupo}&id_usuario=${idUsuario}`);
    
            if (response.ok) {
                const actividades = await response.json();
                if (actividades.length > 0) {
                    // Si hay actividades, mostrarlas en el modal
                    mostrarActividades(actividades);
                } else {
                    // Si no hay actividades, mostrar un mensaje indicando que no hay actividades registradas
                    mostrarActividades([]);
                }
            } else {
                console.error('Error al obtener actividades:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    $('#grupoModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id_grupo = button.data('id-grupo');
        cargarActividades(id_grupo);
        cargarComentarios();
    });
    
    async function mostrarActividades(actividades) {
    const actividadesContainer = $('#actividadesContainer');
    actividadesContainer.empty(); // Vaciar el contenedor de actividades

    if (actividades.length > 0) {
        // Si hay actividades, agregarlas al contenedor
        var idUsuario = sessionStorage.getItem('id');
        var idGrupo = sessionStorage.getItem('id_grupo');
        // Consultar la tabla Documentos para obtener los documentos del alumno
        try {
            for (let actividad of actividades) {
                const idActividad = actividad.id_actividad; // Obtener el id_actividad de la actividad actual
                
                // Consultar la tabla Documentos para obtener los documentos del alumno para una actividad específica
                const response = await fetch(`consultarDocumentos.php?id_alumno=${idUsuario}&id_actividad=${idActividad}`);
                if (!response.ok) {
                    throw new Error('Error en la solicitud');
                }
                const documentos = await response.json();

                const entregada = documentos.length > 0; // Verificar si existen documentos para esta actividad
                
                const contenidoActividad = `
                    <div class="my-cell ${entregada ? 'entregada' : ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn botn-link" data-toggle="collapse" data-target="#activity-${actividad.id_actividad}" aria-expanded="false">${actividad.nombre}</button>
                        </div>
                        <div id="activity-${actividad.id_actividad}" class="collapse">
                            <p>Fecha Entrega: ${actividad.fecha_entrega}</p>
                            <p>Descripción: ${actividad.descripcion}</p>
                            <div><p>Archivos:</p></div>
                            <ul>
                                ${actividad.archivos && actividad.archivos.map((archivo, idx) => `<li style="color:white"><a href="${archivo}" target="_blank" class="enlace-archivo">Archivo ${idx + 1}</a></li>`).join('')}
                            </ul>
                            <!-- Formulario para cargar archivos de respuesta -->
                            <form id="respuestaForm" class="respuesta-form" enctype="multipart/form-data" action="alumnoDocs.php" method="POST">
                                <input type="hidden" name="id_actividad" value=${actividad.id_actividad}>
                                <input type="hidden" name="id_alumno" value=${idUsuario}>
                                <input type="hidden" name="id_grupo" value=${idGrupo}>
                                <div class="form-group">
                                    <label for="archivoRespuesta" class="texto-blanco">Cargar Archivo(s) de Respuesta</label>
                                    <input type="file" class="form-control-file texto-blanco" id="archivoRespuesta" name="archivoRespuesta[]" accept=".pdf,.doc,.docx,.txt,.png,.jpg" multiple>
                                </div>
                                <div class="form-group">
                                    <label for="comentario" class="texto-blanco">Comentario</label>
                                    <textarea class="form-control" id="comentario" name="comentario" rows="3">${entregada ? documentos[0].comentario : ''}</textarea>
                                </div>
                                ${entregada && documentos[0].nombre_archivo ? `<div class="form-group">
                                    <label for="archivoEntregado" class="texto-blanco">Archivo Entregado</label>
                                    <p>${documentos[0].nombre_archivo}</p>
                                </div>` : ''}
                                <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
                            </form>
                        </div>
                    </div>`;
                actividadesContainer.append(contenidoActividad);
            }
        } catch (error) {
            console.error('Error:', error);
        }
        // Agregar evento de escucha para los formularios de respuesta
        $('.respuesta-form').on('submit', async function(event) {
            event.preventDefault(); // Prevenir el envío del formulario por defecto
            const formData = new FormData(this); // Obtener los datos del formulario
        
            try {
                const response = await fetch($(this).attr('action'), {
                    method: $(this).attr('method'), // Obtener el método del formulario
                    body: formData
                });
        
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.status);
                }
        
                const responseData = await response.json();
                console.log(responseData);
        
                // Mostrar un alert con el mensaje de éxito o error
                if (responseData.hasOwnProperty('mensaje')) {
                    // Si la respuesta contiene un mensaje de éxito, mostrar un alert verde
                    alert(responseData.mensaje);
                    $('#grupoModal').modal('hide'); // Cerrar el modal del grupo
                } else if (responseData.hasOwnProperty('error')) {
                    // Si la respuesta contiene un mensaje de error, mostrar un alert rojo
                    alert('Error al enviar la respuesta: ' + responseData.error);
                }
        
                // Aquí puedes actualizar la lista de actividades u otra acción necesaria
            } catch (error) {
                // Manejar errores de la solicitud
                console.error('Error al enviar la respuesta:', error);
                alert('Error al enviar la respuesta: ' + error);
            }
        });
    } else {
        // Si no hay actividades, mostrar un mensaje indicando que no hay actividades registradas
        actividadesContainer.html('<h4>No hay actividades registradas para este grupo.</h4>');
    }
}

// Función para mostrar un comentario en la sección de comentarios
function mostrarComentario(comentario) {
    const comentariosContainer = document.getElementById('comentariosContainer');

    // Crear un nuevo elemento para el comentario
    const comentarioElement = document.createElement('div');
    comentarioElement.classList.add('comentario');
    comentarioElement.innerHTML = `
        <p>${comentario}</p>
    `;

    // Agregar el nuevo comentario al contenedor de comentarios
    comentariosContainer.appendChild(comentarioElement);

    // Agregar un evento de clic al botón de respuesta del nuevo comentario
    const respuestaButton = comentarioElement.querySelector('.btn-primary');
    respuestaButton.addEventListener('click', async () => {
        const respuestaTextarea = comentarioElement.querySelector('textarea');
        const respuesta = respuestaTextarea.value.trim();

        // Verificar que la respuesta no esté vacía
        if (respuesta === '') {
            alert('Por favor, escribe una respuesta');
            return;
        }

        try {
            // Realizar una solicitud POST para enviar la respuesta a la base de datos
            const response = await fetch('guardar_respuesta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ respuesta }),
            });

            if (!response.ok) {
                throw new Error('Error al enviar la respuesta');
            }

            // Limpiar el textarea después de enviar la respuesta
            respuestaTextarea.value = '';

            // Mostrar la respuesta debajo del comentario correspondiente
            mostrarRespuesta(respuesta, comentarioElement);
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error al enviar la respuesta. Por favor, inténtalo de nuevo.');
        }
    });
}

async function cargarComentarios() {
    try {
        // Obtener el ID del grupo almacenado en sessionStorage (o de donde sea que lo obtuviste)
        const idGrupo = sessionStorage.getItem('id_grupo');

        // Realizar una solicitud GET para obtener los comentarios del grupo
        const response = await fetch(`obtener_comentarios.php?id_grupo=${idGrupo}`);

        if (response.ok) {
            const comentarios = await response.json();

            // Mostrar los comentarios en el contenedor correspondiente
            mostrarComentarios(comentarios);
        } else {
            console.error('Error al obtener comentarios:', response.status);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

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

        const h3 = document.createElement('h3');
        h3.textContent = comentario.name;
        principalDiv.appendChild(h3);

        // Agregar el texto del comentario al comentario
        const p = document.createElement('p');
        p.textContent = comentario.texto;
        principalDiv.appendChild(p);

        
        if (comentario.respuestas) {
            // Recorrer las respuestas
            comentario.respuestas.forEach(respuesta => {
              // Crear un elemento div para la respuesta
              const respuestaElement = document.createElement('div');
              respuestaElement.classList.add('respuesta');
              respuestaElement.innerHTML = `
                <p class="text-muted">${respuesta.texto}</p>
              `;
        
              // Agregar la respuesta al comentario principal
              comentarioElement.appendChild(respuestaElement);
            });
        }
        
        registroDiv.appendChild(principalDiv);

        // Agregar el contenedor del nombre y la imagen del perfil al comentario
        comentarioElement.appendChild(registroDiv);
        
        // Agregar el comentario al contenedor de comentarios
        comentariosContainer.appendChild(comentarioElement);
});
    
}

function enviarRespuesta(idPadre, textoRespuesta) {
  // Realizar una solicitud AJAX a guardar_respuesta.php
  fetch('guardar_respuesta.php', {
    method: 'POST',
    body: JSON.stringify({ idPadre, textoRespuesta }),
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    // Verificar si la respuesta se guardó correctamente
    if (data.success) {
      // Recargar los comentarios para mostrar la nueva respuesta
      cargarComentarios();
      // Mostrar un mensaje de éxito
      alert('Su respuesta ha sido enviada.');
    } else {
      // Mostrar un mensaje de error
      alert('Ocurrió un error al enviar la respuesta. Por favor, inténtalo de nuevo.');
      console.error('Error al guardar la respuesta:', data.error);
    }
  })
  .catch(error => {
    // Mostrar un mensaje de error general
    alert('Ocurrió un error al enviar la solicitud. Por favor, inténtalo de nuevo.');
    console.error('Error al enviar la solicitud AJAX:', error);
  });
}

    function borrarComentario(idComentario){
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
    // Función para mostrar el formulario de respuesta
    async function mostrarFormularioRespuesta(comentarioId) {
        try {
            const response = await fetch(`respuestaComentario.php?id=${comentarioId}`);
            const formHTML = await response.text();
            const comentarioElement = document.querySelector(`[data-comentario-id="${comentarioId}"]`);
            if (comentarioElement) {
                comentarioElement.insertAdjacentHTML('beforeend', formHTML);
            }
        } catch (error) {
            console.error('Error al mostrar el formulario de respuesta:', error);
        }
    }

// Función para mostrar una respuesta debajo del comentario correspondiente
function mostrarRespuesta(respuesta, comentarioElement) {
    // Crear un nuevo elemento para la respuesta
    const respuestaElement = document.createElement('div');
    respuestaElement.classList.add('respuesta');
    respuestaElement.innerHTML = `
        <p class="text-muted">${respuesta}</p>
    `;

    // Agregar la respuesta debajo del comentario correspondiente
    comentarioElement.appendChild(respuestaElement);
}