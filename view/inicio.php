<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - PRIME FITNESS</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Estilos Dashboard Paper -->
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
                <div class="text-white-50 small" style="font-size: 0.75rem;">En línea</div>
            </div>
        </div>

        <!-- Módulos de Navegación Lateral -->
        <ul class="sidebar-nav">
            <!-- MÓDULO 1: INICIO -->
            <li class="nav-item">
                <a class="nav-link active" href="index.php?action=inicio">
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
                <a class="nav-link" href="index.php?action=clientes">
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
                <h5 class="page-title">Panel de Control / Inicio</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border px-3 py-2 fw-normal">
                    <i class="bi bi-calendar3 me-1 text-muted"></i> <?= date('d/m/Y') ?>
                </span>
                <a href="index.php?action=logout" class="btn btn-sm btn-outline-secondary rounded-3" title="Salir">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <!-- Cuerpo del Dashboard -->
        <div class="content-body">
            
            <!-- TARJETAS DE ESTADÍSTICAS (Analíticas de Negocio PRIME FITNESS) -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
                <!-- Tarjeta 1: Administradores Creados -->
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-success">
                                <i class="bi bi-person-gear"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Administradores</h6>
                                <h3 id="statTotalUsers"><?= $totalUsuarios ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-shield-lock"></i> Usuarios admin creados
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2: Cantidad de Clientes Registrados -->
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-info">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Clientes</h6>
                                <h3><?= $totalClientes ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-person-check"></i> Clientes registrados
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 3: Cantidad de Pedidos Solicitados -->
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-warning">
                                <i class="bi bi-bag-check-fill"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Pedidos</h6>
                                <h3><?= $totalPedidos ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-cart"></i> Pedidos solicitados
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 4: Ingresos del Mes -->
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-success">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Ingresos del Mes</h6>
                                <h3>$<?= number_format($ingresosMes ?? 0, 2) ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-calendar-check"></i> Mes actual
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 5: Ingresos Totales Adquiridos -->
                <div class="col">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-danger">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Ingresos Totales</h6>
                                <h3>$<?= number_format($ingresosTotales ?? 0, 2) ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-graph-up-arrow"></i> Total adquirido
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILA SECUNDARIA: Banner de Bienvenida y Acceso Directo -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Bienvenido al Sistema de Gestión</h5>
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2">
                                <i class="bi bi-patch-check-fill me-1"></i> Sesión Autenticada
                            </span>
                        </div>
                        <p class="text-secondary leading-relaxed">
                            Has iniciado sesión correctamente como <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></strong> (<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>).
                            Desde este panel puedes supervisar el estado general de la plataforma o ingresar al módulo <strong>Admin</strong> para gestionar altas, bajas y modificaciones de usuarios con AJAX.
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a href="index.php?action=admin" class="btn btn-primary-custom">
                                <i class="bi bi-shield-lock-fill me-1"></i> Gestión de Admin
                            </a>
                            <a href="index.php?action=clientes" class="btn btn-outline-dark rounded-3 px-3 py-2 fw-semibold">
                                <i class="bi bi-people-fill me-1"></i> Gestión de Clientes
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Módulos del Sistema</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="bi bi-grid-fill text-success me-2"></i>
                                    <strong>Inicio</strong>
                                </div>
                                <span class="badge bg-light text-dark">Activo</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="bi bi-shield-lock-fill text-primary me-2"></i>
                                    <strong>Admin</strong>
                                </div>
                                <a href="index.php?action=admin" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2" style="font-size: 0.75rem;">Entrar</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="bi bi-people-fill text-info me-2"></i>
                                    <strong>Clientes</strong>
                                </div>
                                <a href="index.php?action=clientes" class="btn btn-sm btn-outline-info rounded-pill py-0 px-2" style="font-size: 0.75rem;">Entrar</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Controlador del Menú Responsivo -->
<script src="assets/js/sidebar.js"></script>
</body>
</html>
