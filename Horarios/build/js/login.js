document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
});

function iniciarApp() {
    togglePassword();
}

function togglePassword() {
    const toggle = document.querySelector('#toggle-password');
    toggle.addEventListener('click', function() {
        const passwordInput = document.querySelector('#password');
        const toggleText = document.querySelector('#toggle-text');
        const ojo = document.querySelector('#ojo');
        const ver = document.querySelector('#ver');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleText.textContent = 'Ocultar Contraseña';
            ojo.classList.add('ocultar');
            ver.classList.remove('ocultar');
        } else {
            passwordInput.type = 'password';
            toggleText.textContent = 'Mostrar Contraseña';
            ojo.classList.remove('ocultar');
            ver.classList.add('ocultar');
        }
    });
}
