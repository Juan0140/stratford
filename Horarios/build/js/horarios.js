document.addEventListener('DOMContentLoaded',()=>{
    iniciarApp();
})

function iniciarApp(){
    filtrarDias();
}

function filtrarDias(){
    const filtroDia = document.querySelector('#filtro-dia');
    const horarios = document.querySelectorAll('.horario-dia');

    filtroDia.addEventListener('change', (event) => {
        const selectedDia = event.target.value;
        horarios.forEach(horario => {
            if (selectedDia == 0) {
                horario.classList.add('activo');
            } else {
                if (horario.dataset.dia == selectedDia) {
                    horario.classList.add('activo');
                } else {
                    horario.classList.remove('activo');
                }
            }
        });
    });

    // Inicialmente, muestra todos los horarios si "Ninguno" está seleccionado
    const selectedDia = filtroDia.value;
    if (selectedDia == 0) {
        horarios.forEach(horario => {
            horario.classList.add('activo');
        });
    }
}