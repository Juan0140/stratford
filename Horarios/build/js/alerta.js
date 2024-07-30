document.addEventListener('DOMContentLoaded', () => {
    iniciarApp2();
});

function iniciarApp2(){
    alertas();
}

function alertas(){
    const divAlerta = document.querySelector('#alerta');
    if (alerta) {
        setTimeout(function() {
            alerta.style.transition = "opacity 0.5s ease";
            alerta.style.opacity = "0";
            setTimeout(function() {
                alerta.remove();
            }, 500); // Espera a que la transición termine antes de remover el elemento
        }, 4000); // Desaparecer la alerta después de 2 segundos (2000 milisegundos)
    }
}