/**
 * AJAX para Login de Usuarios con SweetAlert2
 */
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnLogin = document.getElementById('btnLogin');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    if (!email || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, ingresa tu correo y contraseña.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    btnLogin.disabled = true;
    btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verificando...';

    try {
        const res = await fetch('index.php?action=login_auth', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();

        if (data.success) {
            // ALERTA DE INICIO DE SESIÓN EXITOSO CON SWEETALERT2
            Swal.fire({
                icon: 'success',
                title: '¡Inicio de Sesión Exitoso!',
                text: 'Bienvenido de nuevo ' + (data.user?.name || '') + '. Redirigiendo al panel...',
                showConfirmButton: false,
                timer: 1600,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'index.php?action=inicio';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error de Autenticación',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
            btnLogin.disabled = false;
            btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión';
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo comunicar con el servidor.',
            confirmButtonColor: '#ef4444'
        });
        btnLogin.disabled = false;
        btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión';
    }
});
