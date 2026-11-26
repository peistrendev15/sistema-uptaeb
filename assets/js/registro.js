/**
 * AJAX para Registro Público con SweetAlert2
 */
const registerForm = document.getElementById('registerForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmPassword');
const btnRegister = document.getElementById('btnRegister');

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!name || !email || !password || !confirmPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor completa todos los campos requeridos.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    if (password.length < 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña débil',
            text: 'La contraseña debe tener mínimo 6 caracteres.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    if (password !== confirmPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Discrepancia',
            text: 'Las contraseñas no coinciden.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    btnRegister.disabled = true;
    btnRegister.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Registrando...';

    try {
        const res = await fetch('index.php?action=registrar_publico', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password })
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Registro Exitoso!',
                text: data.message + ' Redirigiendo al login...',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'index.php?action=login';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error de Registro',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
            btnRegister.disabled = false;
            btnRegister.innerHTML = '<i class="bi bi-check-circle me-1"></i> Registrarse';
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Red',
            text: 'Error de comunicación con el servidor.',
            confirmButtonColor: '#ef4444'
        });
        btnRegister.disabled = false;
        btnRegister.innerHTML = '<i class="bi bi-check-circle me-1"></i> Registrarse';
    }
});
