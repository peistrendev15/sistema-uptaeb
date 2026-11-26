/**
 * Cliente AJAX para el CRUD de Usuarios con Side Modal (Offcanvas) y SweetAlert2
 */

const API_URL = 'index.php';

// Referencias del DOM - Tabla
const userTableBody = document.getElementById('userTableBody');
const refreshBtn = document.getElementById('refreshBtn');
const btnNuevoUsuario = document.getElementById('btnNuevoUsuario');

// Referencias del Side Modal (Offcanvas)
const drawerEl = document.getElementById('userSideDrawer');
const drawerAvatar = document.getElementById('drawerAvatar');
const drawerUserName = document.getElementById('drawerUserName');
const drawerUserCode = document.getElementById('drawerUserCode');
const drawerStatusBadge = document.getElementById('drawerStatusBadge');
const drawerRoleBadge = document.getElementById('drawerRoleBadge');

// Secciones internas del Side Modal
const viewModeSection = document.getElementById('viewModeSection');
const formModeSection = document.getElementById('formModeSection');
const viewModeFooter = document.getElementById('viewModeFooter');
const formModeFooter = document.getElementById('formModeFooter');

// Campos Modo Ver Información
const infoName = document.getElementById('infoName');
const infoCode = document.getElementById('infoCode');
const infoEmail = document.getElementById('infoEmail');
const infoDate = document.getElementById('infoDate');
const infoTime = document.getElementById('infoTime');
const btnSwitchToEdit = document.getElementById('btnSwitchToEdit');
const btnDeleteFromDrawer = document.getElementById('btnDeleteFromDrawer');

// Campos Modo Formulario (Registrar / Editar)
const userForm = document.getElementById('userForm');
const userIdInput = document.getElementById('userId');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const passwordLabel = document.getElementById('passwordLabel');
const passwordHelp = document.getElementById('passwordHelp');
const formSectionTitle = document.getElementById('formSectionTitle');
const submitBtn = document.getElementById('submitBtn');

// Instancia de Bootstrap Offcanvas y usuario seleccionado actual
let sideDrawer = null;
let usuarioActual = null;

document.addEventListener('DOMContentLoaded', () => {
    if (drawerEl) {
        sideDrawer = new bootstrap.Offcanvas(drawerEl);
    }

    // Cargar usuarios al iniciar
    cargarUsuarios();

    // Eventos principales
    if (btnNuevoUsuario) {
        btnNuevoUsuario.addEventListener('click', abrirSideModalRegistro);
    }

    if (refreshBtn) {
        refreshBtn.addEventListener('click', cargarUsuarios);
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', manejarEnvioFormulario);
    }

    if (userForm) {
        userForm.addEventListener('submit', manejarEnvioFormulario);
    }

    if (btnSwitchToEdit) {
        btnSwitchToEdit.addEventListener('click', () => {
            if (usuarioActual) {
                configurarModoEdicion(usuarioActual);
            }
        });
    }

    if (btnDeleteFromDrawer) {
        btnDeleteFromDrawer.addEventListener('click', () => {
            if (usuarioActual) {
                confirmarEliminar(usuarioActual.id, usuarioActual.name);
            }
        });
    }
});

/**
 * 1. OBTENER / LISTAR USUARIOS (AJAX - GET)
 */
async function cargarUsuarios() {
    mostrarCargandoTabla();
    try {
        const res = await fetch(`${API_URL}?action=listar`);
        const data = await res.json();

        if (data.success) {
            renderizarTabla(data.usuarios);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
        }
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Red',
            text: 'No se pudo conectar con el servidor para obtener los usuarios.',
            confirmButtonColor: '#ef4444'
        });
    }
}

function renderizarTabla(usuarios) {
    if (!usuarios || usuarios.length === 0) {
        userTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    No hay usuarios registrados aún.
                </td>
            </tr>
        `;
        return;
    }

    userTableBody.innerHTML = usuarios.map(u => {
        const fecha = u.created_at ? new Date(u.created_at).toLocaleDateString() : 'N/A';
        const iniciales = obtenerIniciales(u.name);
        const codigo = `US-${String(u.id).padStart(3, '0')}`;

        return `
            <tr style="cursor: pointer;" onclick="verDetalleUsuario(${u.id})">
                <td class="ps-4 fw-bold text-primary">${codigo}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="table-user-avatar">${iniciales}</span>
                        <span class="fw-semibold text-dark">${escaparHTML(u.name)}</span>
                    </div>
                </td>
                <td class="text-muted">${escaparHTML(u.email)}</td>
                <td class="text-muted small">${fecha}</td>
                <td>
                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-semibold">
                        Activo
                    </span>
                </td>
                <td class="text-end pe-4" onclick="event.stopPropagation();">
                    <button class="btn btn-sm btn-outline-info me-1 rounded-2 shadow-sm" onclick="verDetalleUsuario(${u.id})" title="Ver información">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-2 shadow-sm" onclick="confirmarEliminar(${u.id}, '${escaparHTML(u.name)}')" title="Eliminar usuario">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * 2. VER INFORMACIÓN EN SIDE MODAL (Estilo Tarjetas de la imagen)
 */
async function verDetalleUsuario(id) {
    try {
        const res = await fetch(`${API_URL}?action=obtener&id=${id}`);
        const data = await res.json();

        if (data.success) {
            const u = data.usuario;
            usuarioActual = u;
            const codigo = `US-${String(u.id).padStart(3, '0')}`;
            const fechaObj = u.created_at ? new Date(u.created_at) : null;

            // Actualizar cabecera oscura del Side Modal
            drawerAvatar.innerText = obtenerIniciales(u.name);
            drawerUserName.innerText = u.name;
            drawerUserCode.innerText = `${u.email} · ${codigo}`;
            drawerStatusBadge.innerText = 'Activo';
            drawerRoleBadge.innerText = 'Cuenta Verificada';

            // Llenar las tarjetas de información personal
            infoName.innerText = u.name;
            infoCode.innerText = codigo;
            infoEmail.innerText = u.email;
            infoDate.innerText = fechaObj ? fechaObj.toLocaleDateString() : 'No especificado';
            infoTime.innerText = fechaObj ? fechaObj.toLocaleTimeString() : 'No especificado';

            // Mostrar sección de lectura y ocultar formulario
            viewModeSection.classList.remove('d-none');
            viewModeFooter.classList.remove('d-none');
            formModeSection.classList.add('d-none');
            formModeFooter.classList.add('d-none');

            if (sideDrawer) sideDrawer.show();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo obtener la información del usuario.' });
    }
}

/**
 * 3. ABRIR SIDE MODAL EN MODO REGISTRO
 */
function abrirSideModalRegistro() {
    usuarioActual = null;
    userForm.reset();
    userIdInput.value = '';

    // Actualizar cabecera oscura para nuevo usuario
    drawerAvatar.innerHTML = '<i class="bi bi-person-plus"></i>';
    drawerUserName.innerText = 'Nuevo Usuario';
    drawerUserCode.innerText = 'Alta de credencial en el sistema';
    drawerStatusBadge.innerText = 'Nuevo Registro';
    drawerRoleBadge.innerText = 'En proceso';

    // Configurar campos de formulario
    formSectionTitle.innerText = 'REGISTRAR NUEVO USUARIO';
    passwordInput.setAttribute('required', 'required');
    passwordLabel.innerText = 'CONTRASEÑA *';
    passwordHelp.classList.add('d-none');

    submitBtn.className = 'btn btn-primary-custom w-50 fw-semibold rounded-3 py-2';
    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Guardar';

    // Mostrar formulario y ocultar modo lectura
    viewModeSection.classList.add('d-none');
    viewModeFooter.classList.add('d-none');
    formModeSection.classList.remove('d-none');
    formModeFooter.classList.remove('d-none');

    if (sideDrawer) sideDrawer.show();
}

/**
 * 4. ABRIR SIDE MODAL EN MODO EDICIÓN
 */
async function iniciarEdicion(id) {
    try {
        const res = await fetch(`${API_URL}?action=obtener&id=${id}`);
        const data = await res.json();

        if (data.success) {
            usuarioActual = data.usuario;
            configurarModoEdicion(usuarioActual);
            if (sideDrawer) sideDrawer.show();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudieron cargar los datos para editar.' });
    }
}

function configurarModoEdicion(u) {
    const codigo = `US-${String(u.id).padStart(3, '0')}`;

    // Actualizar cabecera oscura
    drawerAvatar.innerText = obtenerIniciales(u.name);
    drawerUserName.innerText = u.name;
    drawerUserCode.innerText = `Editando · ${codigo}`;
    drawerStatusBadge.innerText = 'Modo Edición';
    drawerRoleBadge.innerText = u.email;

    // Llenar inputs del formulario
    userIdInput.value = u.id;
    nameInput.value = u.name;
    emailInput.value = u.email;
    passwordInput.value = '';
    passwordInput.removeAttribute('required');

    formSectionTitle.innerText = 'MODIFICAR INFORMACIÓN PERSONAL';
    passwordLabel.innerText = 'NUEVA CONTRASEÑA (OPCIONAL)';
    passwordHelp.classList.remove('d-none');

    submitBtn.className = 'btn btn-warning text-dark w-50 fw-bold rounded-3 py-2';
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Actualizar';

    // Mostrar formulario y ocultar modo lectura
    viewModeSection.classList.add('d-none');
    viewModeFooter.classList.add('d-none');
    formModeSection.classList.remove('d-none');
    formModeFooter.classList.remove('d-none');
}

/**
 * 5. GUARDAR: REGISTRAR O ACTUALIZAR (AJAX - POST)
 */
async function manejarEnvioFormulario(e) {
    if (e) e.preventDefault();

    const id = userIdInput.value;
    const esEdicion = Boolean(id);

    const payload = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim()
    };

    if (passwordInput.value) {
        payload.password = passwordInput.value;
    }

    if (!payload.name || !payload.email) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Nombre y correo electrónico son obligatorios.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    if (!esEdicion && !payload.password) {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña requerida',
            text: 'La contraseña es obligatoria para nuevos registros.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    const accion = esEdicion ? 'actualizar' : 'registrar';
    if (esEdicion) payload.id = parseInt(id, 10);

    deshabilitarBoton(submitBtn, true, esEdicion ? 'Actualizando...' : 'Guardando...');

    try {
        const res = await fetch(`${API_URL}?action=${accion}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
            if (sideDrawer) sideDrawer.hide();
            userForm.reset();
            cargarUsuarios();

            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: data.message,
                showConfirmButton: false,
                timer: 1600,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#ef4444'
            });
        }
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo procesar la solicitud en el servidor.',
            confirmButtonColor: '#ef4444'
        });
    } finally {
        deshabilitarBoton(submitBtn, false, esEdicion ? '<i class="bi bi-arrow-repeat me-1"></i> Actualizar' : '<i class="bi bi-check-circle me-1"></i> Guardar');
    }
}

/**
 * 6. ELIMINAR USUARIO CON SWEETALERT2
 */
function confirmarEliminar(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará definitivamente al usuario "${name}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`${API_URL}?action=eliminar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();

                if (data.success) {
                    if (sideDrawer) sideDrawer.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    cargarUsuarios();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al eliminar',
                        text: data.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al intentar eliminar el usuario.',
                    confirmButtonColor: '#ef4444'
                });
            }
        }
    });
}

/**
 * Utilidades
 */
function obtenerIniciales(nombre) {
    if (!nombre) return 'US';
    const partes = nombre.trim().split(/\s+/);
    if (partes.length === 1) return partes[0].substring(0, 2).toUpperCase();
    return (partes[0][0] + partes[1][0]).toUpperCase();
}

function mostrarCargandoTabla() {
    userTableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                Cargando usuarios...
            </td>
        </tr>
    `;
}

function deshabilitarBoton(btn, deshabilitar, texto) {
    btn.disabled = deshabilitar;
    btn.innerHTML = texto;
}

function escaparHTML(texto) {
    if (!texto) return '';
    return texto
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
