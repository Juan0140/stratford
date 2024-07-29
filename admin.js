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

$(document).ready(function() {
    var agregarAlumnoBtn = document.getElementById('agregarAlumnoBtn');
    var agregarDocenteBtn = document.getElementById('agregarDocenteBtn');
    // Agregar un evento de clic al botón agregarGruposBtn
    $('#agregarGruposBtn').click(function() {
      // Abrir el modal agregarGrupoModal
      $('#agregarGrupoModal').modal('show');
    });
    
    // Agregar un event listener para el evento de clic
    agregarAlumnoBtn.addEventListener('click', function() {
        // Mostrar el modal al hacer clic en el botón
        $('#addAlumnoModal').modal('show');
    });
    
    // Agregar un event listener para el evento de clic
    agregarDocenteBtn.addEventListener('click', function() {
        // Mostrar el modal al hacer clic en el botón
        $('#addDocenteModal').modal('show');
    });
    
    // Función para mostrar/ocultar la contraseña
    $('#togglePasswordAlumno').click(function(){
        var contrasenaInput = $('#contrasenaAlumno');
        var tipo = contrasenaInput.attr('type');
        if(tipo === 'password') {
            contrasenaInput.attr('type', 'text');
        } else {
            contrasenaInput.attr('type', 'password');
        }
    });
    
    // Agregar un evento de submit al formulario del modal "Agregar Alumno"
    $('#formAgregarAlumno').submit(function(event) {
        event.preventDefault(); // Evitar el envío del formulario por defecto

        // Obtener los datos del formulario
        const formData = new FormData(this);

        // Enviar los datos al archivo PHP usando fetch
        fetch('profeRegAlumno.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            // Manejar la respuesta del servidor
            if (data.success) {
                // Si la operación fue exitosa, mostrar un mensaje de éxito
                alert(data.mensaje);
                // Aquí podrías realizar otras acciones como recargar la página o mostrar otra interfaz
            } else {
                // Si ocurrió un error, mostrar un mensaje de error
                alert(data.mensaje);
            }
        })
        .catch(error => {
            // Manejar errores de red u otros errores
            console.error('Error:', error);
            alert('Se produjo un error al procesar la solicitud.');
        });
    });
    
    // Función para cargar los checkboxes de los grupos disponibles
    async function cargarCheckboxes() {
        try {
            // Obtener los grupos disponibles
            const data = await obtenerGrupos();
    
            // Limpiar el contenedor de los checkboxes
            $('#checkboxesContainer').empty();
    
            // Verificar si hay datos en el array
            if (data && data.length > 0) {
                // Iterar sobre cada grupo y agregar un checkbox para cada uno
                data.forEach(function(grupo) {
                    var checkbox = $('<div class="form-check">\
                                        <input class="form-check-input" type="checkbox" value="' + grupo.id + '" id="grupo' + grupo.id + '" name="grupos[]">\
                                        <label class="form-check-label" for="grupo' + grupo.id + '">' + grupo.nombre + '</label>\
                                     </div>');
                    $('#checkboxesContainer').append(checkbox);
                });
            } else {
                // Si no hay datos, mostrar un mensaje indicando que no hay grupos disponibles
                $('#checkboxesContainer').html('<p>No hay grupos disponibles</p>');
            }
        } catch (error) {
            console.error('Error al cargar los grupos:', error);
        }
    }
    
    // Función para cargar los checkboxes de los grupos disponibles en el modal Agregar Nuevo Docente
    async function cargarCheckboxesDocente() {
        try {
            // Obtener los grupos disponibles
            const data = await obtenerGrupos();
    
            // Limpiar el contenedor de los checkboxes
            $('#checkboxesContainerDocente').empty();
    
            // Verificar si hay datos en el array
            if (data && data.length > 0) {
                // Iterar sobre cada grupo y agregar un checkbox para cada uno
                data.forEach(function(grupo) {
                    var checkbox = $('<div class="form-check">\
                                        <input class="form-check-input" type="checkbox" value="' + grupo.id + '" id="grupoDocente' + grupo.id + '" name="grupos[]">\
                                        <label class="form-check-label" for="grupoDocente' + grupo.id + '">' + grupo.nombre + '</label>\
                                     </div>');
                    $('#checkboxesContainerDocente').append(checkbox);
                });
            } else {
                // Si no hay datos, mostrar un mensaje indicando que no hay grupos disponibles
                $('#checkboxesContainerDocente').html('<p>No hay grupos disponibles</p>');
            }
        } catch (error) {
            console.error('Error al cargar los grupos:', error);
        }
    }

    // Llamar a la función para cargar los checkboxes al cargar el modal
    $('#addDocenteModal').on('show.bs.modal', function () {
        cargarCheckboxesDocente();
    });

    // Llamar a la función para cargar los checkboxes al cargar la página
    cargarCheckboxes();
  });
  
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencia al formulario y agregar un event listener para el evento submit
    const formAgregarGrupo = document.getElementById('agregarGrupoForm');
    formAgregarGrupo.addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada
        
        // Obtener los datos del formulario
        const nombreGrupo = document.getElementById('nombreGrupo').value;
        const detallesGrupo = document.getElementById('detallesGrupo').value;
        const iconoGrupo = document.getElementById('iconoGrupo').files[0]; // Obtener el primer archivo seleccionado
        
        // Crear un objeto FormData para enviar los datos del formulario
        const formData = new FormData();
        formData.append('nombre', nombreGrupo);
        formData.append('detalles', detallesGrupo);
        formData.append('icono', iconoGrupo);

        // Enviar la solicitud HTTP POST usando fetch
        fetch('admin-grupo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Convertir la respuesta a JSON
        .then(data => {
            // Manejar la respuesta del servidor
            if (data.success) {
                // Si la operación fue exitosa, mostrar un mensaje de éxito
                alert(data.mensaje);
                location.reload();
                // Aquí podrías realizar otras acciones como recargar la página o mostrar otra interfaz
            } else {
                // Si ocurrió un error, mostrar un mensaje de error
                alert(data.mensaje);
            }
        })
        .catch(error => {
            // Manejar errores de red u otros errores
            console.error('Error:', error);
            alert('Se produjo un error al procesar la solicitud.');
        });
    });
});

// Función para mostrar y ocultar secciones
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    var secciones = document.getElementsByClassName('seccion');
    for (var i = 0; i < secciones.length; i++) {
        // Verificar si la sección actual es la misma que la que se está intentando mostrar
        if (secciones[i].id === seccion) {
            // Si es la misma sección, verificar si ya está visible
            if (secciones[i].style.display === 'block') {
                // Si ya está visible, ocultarla
                secciones[i].style.display = 'none';
                return; // Salir de la función para evitar mostrar la misma sección nuevamente
            }
        } else {
            // Si no es la misma sección, ocultarla
            secciones[i].style.display = 'none';
        }
    }
    // Mostrar la sección seleccionada si no se encontró una sección activa
    document.getElementById(seccion).style.display = 'block';
}

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

document.addEventListener('DOMContentLoaded', async function() {
    async function cargarDocentes() {
        try {
            const response = await fetch('admin.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();

                // Limpiar la lista de docentes existente
                const listaDocentes = document.getElementById('lista-profesores');
                listaDocentes.innerHTML = '';

                // Verificar si hay docentes en la respuesta
                if (data.profesores && data.profesores.length > 0) {
                    // Iterar sobre los datos de los docentes y agregar cada uno a la lista
                    data.profesores.forEach(function(docente) {
                        // Crear elementos HTML para el docente
                        const contenedor = document.createElement('div');
                        contenedor.classList.add('registro');

                        const secundario = document.createElement('div');
                        secundario.id = 'secundario';

                        const imagen = document.createElement('img');
                        imagen.src = docente.foto; // Asignar la URL de la imagen

                        const principal = document.createElement('div');
                        principal.id = 'principal';

                        const nombre = document.createElement('h1');
                        nombre.textContent = docente.name; // Asignar el nombre del docente

                        const id = document.createElement('h3');
                        id.textContent = 'ID: ' + docente.id; // Asignar el ID del docente

                        // Crear el div para los botones de edición y eliminación
                        const botones = document.createElement('div');
                        botones.id = 'botones';

                        // Crear los botones de editar y eliminar
                        const botonEditar = document.createElement('button');
                        botonEditar.type = 'button'; // Establecer el tipo de botón
                        const imgEditar = document.createElement('img');
                        imgEditar.src = '/iconos/edit.png'; // URL de la imagen de editar
                        botonEditar.appendChild(imgEditar);
                        botonEditar.addEventListener('click', function() {
                            mostrarEditarModal(docente, 'docente'); // Pasar 'docente' como tipo
                        });

                        const botonEliminar = document.createElement('button');
                        botonEliminar.type = 'button'; // Establecer el tipo de botón
                        const imgEliminar = document.createElement('img');
                        imgEliminar.src = '/iconos/trash.svg'; // URL de la imagen de eliminar
                        botonEliminar.appendChild(imgEliminar);
                        imgEliminar.addEventListener('click', function() {
                            eliminarRegistro(docente.id, 'administrarDocentes');
                        });

                        // Agregar los botones al div de botones
                        botones.appendChild(botonEditar);
                        botones.appendChild(botonEliminar);

                        // Agregar los elementos al contenedor principal
                        secundario.appendChild(imagen);
                        principal.appendChild(nombre);
                        principal.appendChild(id);
                        contenedor.appendChild(secundario);
                        contenedor.appendChild(principal);
                        contenedor.appendChild(botones);

                        // Agregar el contenedor a la lista de docentes
                        listaDocentes.appendChild(contenedor);
                    });
                } else {
                    // Si no hay docentes, mostrar un mensaje indicando que no hay datos
                    const mensaje = document.createElement('div');
                    mensaje.textContent = 'No hay docentes disponibles';
                    listaDocentes.appendChild(mensaje);
                }

                // Mostrar la sección "Administrar Docentes"
                mostrarSeccion('administrarDocentes');
            } else {
                console.error('Error en la solicitud:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Función para cargar la imagen previa
    function cargarImagenPrevia(input, imagen) {
        const defaultFile = 'https://www.pngall.com/wp-content/uploads/5/Profile-Avatar-PNG-Picture.png';
        input.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagen.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                imagen.src = defaultFile;
            }
        });
    }
    
    // Cargar imagen previa para el formulario de docente
    const fileDocente = document.getElementById('fotoDocente');
    const imgDocente = document.getElementById('imgADocente');
    cargarImagenPrevia(fileDocente, imgDocente);
    
    // Cargar imagen previa para el formulario de grupo
    const fileGrupo = document.getElementById('iconoGrupo');
    const imgGrupo = document.getElementById('imgGrupo');
    cargarImagenPrevia(fileGrupo, imgGrupo);
    
    // Cargar imagen previa para el formulario de alumno
    const fileAlumno = document.getElementById('fotoAlumno');
    const imgAlumno = document.getElementById('imgAlumno');
    cargarImagenPrevia(fileAlumno, imgAlumno);
    
    // Evento para abrir el campo de entrada de archivo al hacer clic en el botón
    document.getElementById('cargarFotoDocenteBtn').addEventListener('click', function() {
        document.getElementById('fotoDocente').click();
    });
    
    // Evento para abrir el campo de entrada de archivo al hacer clic en el botón
    document.getElementById('cargarIconoBtn').addEventListener('click', function() {
        document.getElementById('iconoGrupo').click();
    });
    
    // Evento para abrir el campo de entrada de archivo al hacer clic en el botón
    document.getElementById('cargarFotoBtn').addEventListener('click', function() {
        document.getElementById('fotoAlumno').click();
    });
    
    // Modificar la función eliminarRegistro para mostrar el modal de confirmación
    function eliminarRegistro(idRegistro, tipo) {
        // Mostrar el modal de confirmación
        $('#confirmarEliminarModal').modal('show');
        
        // Al hacer clic en el botón de eliminar dentro del modal
        $('#btnConfirmarEliminar').off('click').on('click', function() {
            // Aquí puedes realizar la lógica de eliminación con el ID del registro
            // Por ejemplo, llamar a una función que realice la eliminación
            eliminarDocente(idRegistro);
        });
    
        // Establecer el atributo data-id del botón de eliminar del modal
        $('#btnConfirmarEliminar').data('id', idRegistro);
    }
    
    // Función para eliminar el docente
    async function eliminarDocente(idDocente) {
        try {
            const response = await fetch('admin-del.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + idDocente
            });
    
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Recargar la lista de docentes después de la eliminación
                    cargarDocentes();
                } else {
                    console.error('Error al eliminar el docente:', data.error);
                }
            } else {
                console.error('Error en la solicitud:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Función para mostrar el modal de edición con los datos del docente seleccionado
    function mostrarEditarModal(docente) {
        // Obtener referencias a los campos del formulario de edición
        const editId = document.getElementById('editId');
        const editName = document.getElementById('editName');
        const editUser = document.getElementById('editUser');
    
        // Asignar los valores del docente seleccionado a los campos del formulario
        editId.value = docente.id;
        editName.value = docente.name;
        editUser.value = docente.user;
    
        // Mostrar el modal de edición
        $('#editModal').modal('show');
    }
    
    // Función para enviar el formulario de edición al backend
    async function actualizarRegistro() {
        // Obtener los datos del formulario de edición
        const id = document.getElementById('editId').value;
        const name = document.getElementById('editName').value;
        const user = document.getElementById('editUser').value;
        const pass = document.getElementById('editPass').value;
        const foto = document.getElementById('editFoto').files[0];
    
        // Crear un objeto FormData para enviar los datos del formulario al backend
        const formData = new FormData();
        formData.append('id', id);
        formData.append('name', name);
        formData.append('usuario', user);
        formData.append('pass', pass);
        formData.append('foto', foto);
    
        try {
            const response = await fetch('admin-edit.php', {
                method: 'POST',
                body: formData
            });
    
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Recargar la lista de docentes después de la actualización
                    cargarDocentes();
                } else {
                    console.error('Error al actualizar el registro:', data.message);
                }
            } else {
                console.error('Error en la solicitud:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Asignar el evento click al botón de actualizar del modal de edición
    document.getElementById('actualizarBtn').addEventListener('click', actualizarRegistro);
    
    async function cargarAlumnos() {
    try {
        const response = await fetch('admin.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();

            // Limpiar la lista de alumnos existente
            const listaAlumnos = document.getElementById('lista-alumnos');
            listaAlumnos.innerHTML = '';

            // Verificar si hay alumnos en la respuesta
            if (data.alumnos && data.alumnos.length > 0) {
                // Iterar sobre los datos de los alumnos y agregar cada uno a la lista
                data.alumnos.forEach(function(alumno) {
                    // Crear elementos HTML para el alumno
                    const contenedor = document.createElement('div');
                    contenedor.classList.add('registro');

                    const secundario = document.createElement('div');
                    secundario.id = 'secundario';

                    const imagen = document.createElement('img');
                    imagen.src = alumno.foto; // Asignar la URL de la imagen

                    const principal = document.createElement('div');
                    principal.id = 'principal';

                    const nombre = document.createElement('h1');
                    nombre.textContent = alumno.name; // Asignar el nombre del alumno

                    const id = document.createElement('h3');
                    id.textContent = 'ID: ' + alumno.id; // Asignar el ID del alumno

                    // Crear el div para los botones de edición y eliminación
                    const botones = document.createElement('div');
                    botones.id = 'botones';
                    
                    // Crear el botón de eliminar para el alumno
                    const botonEliminar = document.createElement('button');
                    botonEliminar.type = 'button'; // Establecer el tipo de botón
                    const imgEliminar = document.createElement('img');
                    imgEliminar.src = '/iconos/trash.svg'; // URL de la imagen de eliminar
                    botonEliminar.appendChild(imgEliminar); // Agregar la imagen como hijo del botón
                    botonEliminar.addEventListener('click', function() {
                        eliminarAlumno(alumno.id);
                    });
                    
                    // Agregar el botón de eliminar al div de botones
                    botones.appendChild(botonEliminar);
                    
                    // Agregar los botones al contenedor principal
                    secundario.appendChild(imagen);
                    principal.appendChild(nombre);
                    principal.appendChild(id);
                    contenedor.appendChild(secundario);
                    contenedor.appendChild(principal);
                    contenedor.appendChild(botones); // Agregar el div de botones al contenedor

                    // Agregar el contenedor a la lista de alumnos
                    listaAlumnos.appendChild(contenedor);
                });
            } else {
                // Si no hay alumnos, mostrar un mensaje indicando que no hay datos
                const mensaje = document.createElement('div');
                mensaje.textContent = 'No hay alumnos disponibles';
                listaAlumnos.appendChild(mensaje);
            }

            // Mostrar la sección "Administrar Alumnos"
            mostrarSeccion('administrarAlumnos');
        } else {
            console.error('Error en la solicitud:', response.status);
        }
    } catch (error) {
        console.error('Error:', error);
    }
    }
    
    async function eliminarAlumno(idAlumno) {
        try {
            const response = await fetch('admin-del.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + idAlumno
            });
    
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Recargar la lista de alumnos después de la eliminación
                    cargarAlumnos();
                } else {
                    console.error('Error al eliminar el alumno:', data.error);
                }
            } else {
                console.error('Error en la solicitud:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function cargarGrupos() {
    try {
        const response = await fetch('admin.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();

            // Limpiar la lista de grupos existente
            const listaGrupos = document.getElementById('gruposContainer');
            listaGrupos.innerHTML = '';

            // Verificar si hay grupos en la respuesta
            if (data.grupos && data.grupos.length > 0) {
                // Iterar sobre los datos de los grupos y agregar cada uno a la lista
                data.grupos.forEach(function(grupo) {
                    // Crear div contenedor
                    const contenedor = document.createElement('div');
                    contenedor.classList.add('registro');

                    // Crear div secundario para la imagen
                    const secundario = document.createElement('div');
                    secundario.id = 'secundario';

                    // Crear imagen
                    const imagen = document.createElement('img');
                    imagen.src = grupo.icono; // Asignar la URL del icono

                    // Agregar imagen al div secundario
                    secundario.appendChild(imagen);

                    // Crear div principal para el nombre y el ID
                    const principal = document.createElement('div');
                    principal.id = 'principal';

                    // Crear nombre
                    const nombre = document.createElement('h1');
                    nombre.textContent = grupo.nombre; // Asignar el nombre del grupo

                    // Crear ID
                    const id = document.createElement('h3');
                    id.textContent = 'ID: ' + grupo.id; // Asignar el ID del grupo

                    // Agregar nombre e ID al div principal
                    principal.appendChild(nombre);
                    principal.appendChild(id);

                    // Crear div para los botones de edición y eliminación
                    const botones = document.createElement('div');
                    botones.id = 'botones';

                    // Crear botón de eliminar
                    const botonEliminar = document.createElement('button');
                    botonEliminar.type = 'button'; // Establecer el tipo de botón
                    const imgEliminar = document.createElement('img');
                    imgEliminar.src = '/iconos/trash.svg'; // URL de la imagen de eliminar
                    botonEliminar.appendChild(imgEliminar); // Agregar la imagen como hijo del botón
                    botonEliminar.addEventListener('click', function() {
                        eliminarGrupo(grupo.id);
                    });

                    // Agregar botón de eliminar al div de botones
                    botones.appendChild(botonEliminar);

                    // Agregar div secundario, principal y botones al contenedor
                    contenedor.appendChild(secundario);
                    contenedor.appendChild(principal);
                    contenedor.appendChild(botones);

                    // Agregar el contenedor a la lista de grupos
                    listaGrupos.appendChild(contenedor);
                });
            } else {
                // Si no hay grupos, mostrar un mensaje indicando que no hay datos
                const mensaje = document.createElement('div');
                mensaje.textContent = 'No hay grupos disponibles';
                listaGrupos.appendChild(mensaje);
            }

            // Mostrar la sección "Administrar Grupos"
            mostrarSeccion('administrarGrupos');
        } else {
            console.error('Error en la solicitud:', response.status);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

    async function eliminarGrupo(idGrupo) {
        try {
            const response = await fetch('adminBorrarGrupo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + idGrupo
            });
    
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Recargar la lista de grupos después de la eliminación
                    cargarGrupos();
                } else {
                    console.error('Error al eliminar el grupo:', data.mensaje);
                }
            } else {
                console.error('Error en la solicitud:', response.status);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Manejador de clic para el botón ".btn-adminprofesor"
    document.querySelector('.btn-admingroup').addEventListener('click', cargarGrupos);
    document.querySelector('.btn-addalumno').addEventListener('click', cargarAlumnos);
    document.querySelector('.btn-adminprofesor').addEventListener('click', cargarDocentes);

    // Otras funciones y eventos aquí...
});