<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Gestión de Clientes - PRIME FITNESS</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Estilos Dashboard PRIME FITNESS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="wrapper">
    <!-- Overlay oscuro para cerrar menú en móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- BARRA LATERAL (SIDEBAR) -->
    <aside class="sidebar">
        <!-- Logo de Marca -->
        <div class="sidebar-brand">
            <div class="brand-logo">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-white tracking-wide">PRIME FITNESS</h6>
                <small class="text-white-50" style="font-size: 0.72rem;">SISTEMA PRO</small>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Cerrar menú">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Perfil del Usuario Activo -->
        <div class="user-profile">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="text-truncate">
                <div class="text-white fw-semibold small text-truncate">
                    <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuario') ?>
                </div>
                <div class="text-white-50 small" style="font-size: 0.75rem;">Administrador</div>
            </div>
        </div>

        <!-- Módulos de Navegación Lateral -->
        <ul class="sidebar-nav">
            <!-- MÓDULO 1: INICIO -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=inicio">
                    <i class="bi bi-grid-fill"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <!-- MÓDULO 2: ADMIN (GESTIÓN DE USUARIOS ADMIN) -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=admin">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Admin</span>
                </a>
            </li>

            <!-- MÓDULO 3: CLIENTES (GESTIÓN DE CLIENTES) -->
            <li class="nav-item">
                <a class="nav-link active" href="index.php?action=clientes">
                    <i class="bi bi-people-fill"></i>
                    <span>Clientes</span>
                </a>
            </li>
        </ul>

        <!-- Botón Salir al pie del Sidebar -->
        <div class="p-3 border-top border-secondary border-opacity-25">
            <a href="index.php?action=logout" class="btn btn-outline-danger w-100 btn-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- PANEL PRINCIPAL DE CONTENIDO -->
    <main class="main-panel">
        <!-- Barra Superior (Navbar) -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="hamburger-btn" id="sidebarToggleBtn" aria-label="Abrir menú">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="page-title">Gestión de Clientes</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary-custom btn-sm rounded-3 shadow-sm" id="btnNuevoCliente">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Cliente
                </button>
                <button class="btn btn-outline-secondary btn-sm rounded-3" id="refreshBtn" title="Actualizar lista">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </header>

        <!-- Cuerpo del Dashboard -->
        <div class="content-body">
            
            <!-- Tabla de Clientes -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-people-fill me-2 text-success"></i>Clientes Registrados
                        </h6>
                        <small class="text-muted">Haz clic en cualquier cliente o botón de acción para abrir el panel lateral</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-4">ID</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">CORREO ELECTRÓNICO</th>
                                    <th scope="col">FECHA REGISTRO</th>
                                    <th scope="col">PEDIDOS</th>
                                    <th scope="col">ESTADO</th>
                                    <th scope="col" class="text-end pe-4">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="clientTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                        Cargando clientes...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- =========================================================================
     SIDE MODAL / DRAWER LATERAL DERECHO (Para Ver Info, Editar y Registrar Cliente)
     ========================================================================= -->
<div class="offcanvas offcanvas-end side-drawer" tabindex="-1" id="clientSideDrawer" aria-labelledby="drawerClientName">
    
    <!-- CABECERA OSCURA DEL SIDE MODAL -->
    <div class="drawer-dark-header">
        <button type="button" class="drawer-close-btn" data-bs-dismiss="offcanvas" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>

        <!-- Identidad principal del cliente -->
        <div class="d-flex align-items-center gap-3">
            <div class="drawer-avatar" id="drawerAvatar">
                CL
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-white" id="drawerClientName">Nuevo Cliente</h5>
                <div class="text-white-50 small mb-2" id="drawerClientCode">Registro en sistema</div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="drawer-badge-active" id="drawerStatusBadge">Activo</span>
                    <span class="drawer-badge-role" id="drawerRoleBadge">Cliente PRIME FITNESS</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CUERPO DEL SIDE MODAL -->
    <div class="drawer-body-content">
        
        <!-- ==========================================================
             MODO 1: VER INFORMACIÓN DEL CLIENTE (Tarjetas de Lectura)
             ========================================================== -->
        <div id="viewModeSection" class="d-none">
            <div class="drawer-section-title">Información Personal del Cliente</div>
            
            <div class="info-grid mb-4">
                <div class="info-box">
                    <span class="info-label">Nombre Completo</span>
                    <span class="info-value" id="infoName">--</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Código de Cliente</span>
                    <span class="info-value" id="infoCode">--</span>
                </div>

                <div class="info-box full-width">
                    <span class="info-label">Correo Electrónico</span>
                    <span class="info-value" id="infoEmail">--</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Fecha de Registro</span>
                    <span class="info-value" id="infoDate">--</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Hora de Registro</span>
                    <span class="info-value" id="infoTime">--</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Pedidos Solicitados</span>
                    <span class="info-value text-primary">0 pedidos</span>
                </div>

                <div class="info-box">
                    <span class="info-label">Estado de Cuenta</span>
                    <span class="info-value text-success">Activo</span>
                </div>
            </div>
        </div>

        <!-- ==========================================================
             MODO 2: FORMULARIO DE REGISTRO Y EDICIÓN DE CLIENTE
             ========================================================== -->
        <div id="formModeSection">
            <div class="drawer-section-title" id="formSectionTitle">Datos del Cliente</div>

            <form id="clientForm" novalidate>
                <input type="hidden" id="clientId" name="id">

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold text-secondary small">NOMBRE COMPLETO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ej: Patricia Dev" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary small">CORREO ELECTRÓNICO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="cliente@correo.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-secondary small" id="passwordLabel">CONTRASEÑA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <small class="text-muted d-none mt-1" id="passwordHelp">
                        <i class="bi bi-info-circle me-1"></i>Dejar en blanco si deseas conservar la contraseña actual.
                    </small>
                </div>
            </form>
        </div>

    </div>

    <!-- PIE FIJO DEL SIDE MODAL -->
    <div class="drawer-footer-bar">
        <!-- Botones cuando está en Modo Ver Información -->
        <div id="viewModeFooter" class="d-none">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-dark w-100 fw-semibold rounded-3 py-2" id="btnSwitchToEdit">
                    <i class="bi bi-pencil me-1"></i> Editar
                </button>
                <button type="button" class="btn btn-outline-danger px-3 rounded-3" id="btnDeleteFromDrawer" title="Eliminar Cliente">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- Botones cuando está en Modo Formulario (Registrar / Editar) -->
        <div id="formModeFooter">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary w-50 rounded-3 py-2" data-bs-dismiss="offcanvas">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary-custom w-50 fw-semibold rounded-3 py-2" id="submitBtn">
                    <i class="bi bi-check-circle me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>

</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Controlador del Menú Responsivo -->
<script src="assets/js/sidebar.js"></script>
<!-- Cliente AJAX de Gestión de Clientes -->
<script src="assets/js/clientes.js"></script>
</body>
</html>
