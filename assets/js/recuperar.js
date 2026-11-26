/**
 * AJAX para Recuperación de Contraseña con SweetAlert2
 */
const recoveryForm = document.getElementById('recoveryForm');
const emailInput = document.getElementById('email');
const nuevaClaveInput = document.getElementById('nuevaClave');
const confirmarClaveInput = document.getElementById('confirmarClave');
const btnRestablecer = document.getElementById('btnRestablecer');

recoveryForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const nuevaClave = nuevaClaveInput.value;
    const confirmarClave = confirmarClaveInput.value;

    if (!email || !nuevaClave || !confirmarClave) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor completa todos los campos del formulario.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    if (nuevaClave.length < 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña corta',
            text: 'La nueva contraseña debe tener al menos 6 caracteres.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    if (nuevaClave !== confirmarClave) {
        Swal.fire({
            icon: 'warning',
            title: 'Error de coincidencia',
            text: 'Las contraseñas no coinciden.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    btnRestablecer.disabled = true;
    btnRestablecer.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Restableciendo...';

    try {
        const res = await fetch('index.php?action=restablecer_clave', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                nueva_clave: nuevaClave,
                confirmar_clave: confirmarClave
            })
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña Restablecida!',
                text: data.message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'index.php?action=login';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
            btnRestablecer.disabled = false;
            btnRestablecer.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Restablecer Contraseña';
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Red',
            text: 'Error de comunicación con el servidor.',
            confirmButtonColor: '#ef4444'
        });
        btnRestablecer.disabled = false;
        btnRestablecer.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Restablecer Contraseña';
    }
});
