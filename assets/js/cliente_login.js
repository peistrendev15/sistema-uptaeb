/**
 * AJAX para Login de Clientes con SweetAlert2
 */
const clientLoginForm = document.getElementById('clientLoginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnClientLogin = document.getElementById('btnClientLogin');

if (clientLoginForm) {
    clientLoginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        if (!email || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor, ingresa tu correo y contraseña de cliente.',
                confirmButtonColor: '#6bd098'
            });
            return;
        }

        btnClientLogin.disabled = true;
        btnClientLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verificando...';

        try {
            const res = await fetch('index.php?action=cliente_login_auth', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Inicio de Sesión Exitoso!',
                    text: 'Bienvenido ' + (data.client?.name || '') + '. Cargando tu portal...',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = 'index.php?action=cliente_dashboard';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso Denegado',
                    text: data.message,
                    confirmButtonColor: '#ef4444'
                });
                btnClientLogin.disabled = false;
                btnClientLogin.innerHTML = '<span>Ingresar como Cliente</span><i class="bi bi-arrow-right"></i>';
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo comunicar con el servidor.',
                confirmButtonColor: '#ef4444'
            });
            btnClientLogin.disabled = false;
            btnClientLogin.innerHTML = '<span>Ingresar como Cliente</span><i class="bi bi-arrow-right"></i>';
        }
    });
}
