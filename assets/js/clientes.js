/**
 * Cliente AJAX para el CRUD Administrativo de Clientes con Side Modal (Offcanvas) y SweetAlert2
 */

const API_URL = 'index.php';

// Referencias del DOM - Tabla
const clientTableBody = document.getElementById('clientTableBody');
const refreshBtn = document.getElementById('refreshBtn');
const btnNuevoCliente = document.getElementById('btnNuevoCliente');

// Referencias del Side Modal (Offcanvas)
const drawerEl = document.getElementById('clientSideDrawer');
const drawerAvatar = document.getElementById('drawerAvatar');
const drawerClientName = document.getElementById('drawerClientName');
const drawerClientCode = document.getElementById('drawerClientCode');
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
const clientForm = document.getElementById('clientForm');
const clientIdInput = document.getElementById('clientId');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const passwordLabel = document.getElementById('passwordLabel');
const passwordHelp = document.getElementById('passwordHelp');
const formSectionTitle = document.getElementById('formSectionTitle');
const submitBtn = document.getElementById('submitBtn');

// Instancia de Bootstrap Offcanvas y cliente seleccionado actual
let sideDrawer = null;
let clienteActual = null;

document.addEventListener('DOMContentLoaded', () => {
    if (drawerEl) {
        sideDrawer = new bootstrap.Offcanvas(drawerEl);
    }

    // Cargar clientes al iniciar
    cargarClientes();

    // Eventos principales
    if (btnNuevoCliente) {
        btnNuevoCliente.addEventListener('click', abrirSideModalRegistro);
    }

    if (refreshBtn) {
        refreshBtn.addEventListener('click', cargarClientes);
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', manejarEnvioFormulario);
    }

    if (clientForm) {
        clientForm.addEventListener('submit', manejarEnvioFormulario);
    }

    if (btnSwitchToEdit) {
        btnSwitchToEdit.addEventListener('click', () => {
            if (clienteActual) {
                configurarModoEdicion(clienteActual);
            }
        });
    }

    if (btnDeleteFromDrawer) {
        btnDeleteFromDrawer.addEventListener('click', () => {
            if (clienteActual) {
                confirmarEliminar(clienteActual.id, clienteActual.name);
            }
        });
    }
});

/**
 * 1. OBTENER / LISTAR CLIENTES (AJAX - GET)
 */
async function cargarClientes() {
    mostrarCargandoTabla();
    try {
        const res = await fetch(`${API_URL}?action=cliente_listar`);
        const data = await res.json();

        if (data.success) {
            renderizarTabla(data.clientes);
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
            text: 'No se pudo conectar con el servidor para obtener los clientes.',
            confirmButtonColor: '#ef4444'
        });
    }
}

function renderizarTabla(clientes) {
    if (!clientes || clientes.length === 0) {
        clientTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    No hay clientes registrados aún.
                </td>
            </tr>
        `;
        return;
    }

    clientTableBody.innerHTML = clientes.map(c => {
        const fecha = c.created_at ? new Date(c.created_at).toLocaleDateString() : 'N/A';
        const iniciales = obtenerIniciales(c.name);
        const codigo = `CL-${String(c.id).padStart(3, '0')}`;

        return `
            <tr style="cursor: pointer;" onclick="verDetalleCliente(${c.id})">
                <td class="ps-4 fw-bold text-primary">${codigo}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="table-user-avatar">${iniciales}</span>
                        <span class="fw-semibold text-dark">${escaparHTML(c.name)}</span>
                    </div>
                </td>
                <td class="text-muted">${escaparHTML(c.email)}</td>
                <td class="text-muted small">${fecha}</td>
                <td>
                    <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">
                        0 pedidos
                    </span>
                </td>
                <td>
                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-semibold">
                        Activo
                    </span>
                </td>
                <td class="text-end pe-4" onclick="event.stopPropagation();">
                    <button class="btn btn-sm btn-outline-info me-1 rounded-2 shadow-sm" onclick="verDetalleCliente(${c.id})" title="Ver información">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-2 shadow-sm" onclick="confirmarEliminar(${c.id}, '${escaparHTML(c.name)}')" title="Eliminar cliente">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * 2. VER INFORMACIÓN EN SIDE MODAL
 */
async function verDetalleCliente(id) {
    try {
        const res = await fetch(`${API_URL}?action=cliente_obtener&id=${id}`);
        const data = await res.json();

        if (data.success) {
            const c = data.cliente;
            clienteActual = c;
            const codigo = `CL-${String(c.id).padStart(3, '0')}`;
            const fechaObj = c.created_at ? new Date(c.created_at) : null;

            drawerAvatar.innerText = obtenerIniciales(c.name);
            drawerClientName.innerText = c.name;
            drawerClientCode.innerText = `${c.email} · ${codigo}`;
            drawerStatusBadge.innerText = 'Activo';
            drawerRoleBadge.innerText = 'Cliente Verificado';

            infoName.innerText = c.name;
            infoCode.innerText = codigo;
            infoEmail.innerText = c.email;
            infoDate.innerText = fechaObj ? fechaObj.toLocaleDateString() : 'No especificado';
            infoTime.innerText = fechaObj ? fechaObj.toLocaleTimeString() : 'No especificado';

            viewModeSection.classList.remove('d-none');
            viewModeFooter.classList.remove('d-none');
            formModeSection.classList.add('d-none');
            formModeFooter.classList.add('d-none');

            if (sideDrawer) sideDrawer.show();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo obtener la información del cliente.' });
    }
}

/**
 * 3. ABRIR SIDE MODAL EN MODO REGISTRO
 */
function abrirSideModalRegistro() {
    clienteActual = null;
    clientForm.reset();
    clientIdInput.value = '';

    drawerAvatar.innerHTML = '<i class="bi bi-person-plus"></i>';
    drawerClientName.innerText = 'Nuevo Cliente';
    drawerClientCode.innerText = 'Alta de cliente en PRIME FITNESS';
    drawerStatusBadge.innerText = 'Nuevo Registro';
    drawerRoleBadge.innerText = 'En proceso';

    formSectionTitle.innerText = 'REGISTRAR NUEVO CLIENTE';
    passwordInput.setAttribute('required', 'required');
    passwordLabel.innerText = 'CONTRASEÑA *';
    passwordHelp.classList.add('d-none');

    submitBtn.className = 'btn btn-primary-custom w-50 fw-semibold rounded-3 py-2';
    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Guardar';

    viewModeSection.classList.add('d-none');
    viewModeFooter.classList.add('d-none');
    formModeSection.classList.remove('d-none');
    formModeFooter.classList.remove('d-none');

    if (sideDrawer) sideDrawer.show();
}

/**
 * 4. CONFIGURAR MODO EDICIÓN DESDE EL SIDE MODAL
 */
function configurarModoEdicion(c) {
    const codigo = `CL-${String(c.id).padStart(3, '0')}`;

    drawerAvatar.innerText = obtenerIniciales(c.name);
    drawerClientName.innerText = c.name;
    drawerClientCode.innerText = `Editando · ${codigo}`;
    drawerStatusBadge.innerText = 'Modo Edición';
    drawerRoleBadge.innerText = c.email;

    clientIdInput.value = c.id;
    nameInput.value = c.name;
    emailInput.value = c.email;
    passwordInput.value = '';
    passwordInput.removeAttribute('required');

    formSectionTitle.innerText = 'MODIFICAR INFORMACIÓN DEL CLIENTE';
    passwordLabel.innerText = 'NUEVA CONTRASEÑA (OPCIONAL)';
    passwordHelp.classList.remove('d-none');

    submitBtn.className = 'btn btn-warning text-dark w-50 fw-bold rounded-3 py-2';
    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Actualizar';

    viewModeSection.classList.add('d-none');
    viewModeFooter.classList.add('d-none');
    formModeSection.classList.remove('d-none');
    formModeFooter.classList.remove('d-none');
}

/**
 * 5. GUARDAR: REGISTRAR O ACTUALIZAR CLIENTE (AJAX - POST)
 */
async function manejarEnvioFormulario(e) {
    if (e) e.preventDefault();

    const id = clientIdInput.value;
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
            text: 'La contraseña es obligatoria para nuevos clientes.',
            confirmButtonColor: '#6bd098'
        });
        return;
    }

    const accion = esEdicion ? 'cliente_actualizar' : 'cliente_registrar';
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
            clientForm.reset();
            cargarClientes();

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
 * 6. ELIMINAR CLIENTE CON SWEETALERT2
 */
function confirmarEliminar(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Se eliminará definitivamente al cliente "${name}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fetch(`${API_URL}?action=cliente_eliminar`, {
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
                    cargarClientes();
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
                    text: 'Ocurrió un error al intentar eliminar el cliente.',
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
    if (!nombre) return 'CL';
    const partes = nombre.trim().split(/\s+/);
    if (partes.length === 1) return partes[0].substring(0, 2).toUpperCase();
    return (partes[0][0] + partes[1][0]).toUpperCase();
}

function mostrarCargandoTabla() {
    clientTableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                Cargando clientes...
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
