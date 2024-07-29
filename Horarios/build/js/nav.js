document.addEventListener('DOMContentLoaded',()=>{
    iniciarApp1();
})

function iniciarApp1(){
    toogleNav();
}

function toogleNav(){
    const toggle = document.querySelector('#toggle-nav')
    const hiper = document.querySelector('.hiper-nav')
    toggle.addEventListener('click', ()=>{
        hiper.classList.toggle('mostrar-nav')
        toggle.classList.toggle('rotate-nav')
    })
}