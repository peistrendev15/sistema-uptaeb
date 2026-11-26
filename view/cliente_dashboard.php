<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Portal - PRIME FITNESS</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Estilos PRIME FITNESS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="wrapper">
    <!-- Overlay oscuro para cerrar menú en móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- BARRA LATERAL (SIDEBAR CLIENTE) -->
    <aside class="sidebar">
        <!-- Logo de Marca -->
        <div class="sidebar-brand">
            <div class="brand-logo">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-white tracking-wide">PRIME FITNESS</h6>
                <small class="text-white-50" style="font-size: 0.72rem;">PORTAL CLIENTES</small>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Cerrar menú">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Perfil del Cliente Activo -->
        <div class="user-profile">
            <div class="user-avatar">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="text-truncate">
                <div class="text-white fw-semibold small text-truncate">
                    <?= htmlspecialchars($_SESSION['client']['name'] ?? 'Cliente') ?>
                </div>
                <div class="text-white-50 small" style="font-size: 0.75rem;">Cliente Activo</div>
            </div>
        </div>

        <!-- Menú Lateral Cliente -->
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.php?action=cliente_dashboard">
                    <i class="bi bi-bag-check-fill"></i>
                    <span>Mis Pedidos</span>
                </a>
            </li>
        </ul>

        <!-- Botón Salir -->
        <div class="p-3 border-top border-secondary border-opacity-25">
            <a href="index.php?action=cliente_logout" class="btn btn-outline-danger w-100 btn-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- PANEL PRINCIPAL DEL CLIENTE -->
    <main class="main-panel">
        <!-- Barra Superior -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="hamburger-btn" id="sidebarToggleBtn" aria-label="Abrir menú">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="page-title">Mi Historial de Pedidos</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border px-3 py-2 fw-normal">
                    <i class="bi bi-calendar3 me-1 text-muted"></i> <?= date('d/m/Y') ?>
                </span>
                <a href="index.php?action=cliente_logout" class="btn btn-sm btn-outline-secondary rounded-3" title="Salir">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </header>

        <!-- Cuerpo del Dashboard de Cliente -->
        <div class="content-body">
            
            <!-- TARJETA PRINCIPAL DE PEDIDOS SOLICITADOS -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 mb-0">
                        <div class="stat-card">
                            <div class="icon-big icon-success">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <div class="stat-info">
                                <h6>Pedidos Solicitados</h6>
                                <h3><?= $pedidosSolicitados ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="stat-card-footer">
                            <i class="bi bi-clock-history"></i> Total en tu historial
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE PEDIDOS DEL CLIENTE -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">
                                    <i class="bi bi-clock-history me-2 text-success"></i>Historial de Solicitudes y Pedidos
                                </h6>
                                <small class="text-muted">Registro de todos los pedidos realizados con tu cuenta</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="ps-4">#PEDIDO</th>
                                            <th scope="col">DESCRIPCIÓN</th>
                                            <th scope="col">FECHA</th>
                                            <th scope="col">MONTO</th>
                                            <th scope="col" class="text-end pe-4">ESTADO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                                <div class="fw-semibold text-dark">Aún no has solicitado ningún pedido (0 pedidos)</div>
                                                <small>Cuando realices solicitudes en PRIME FITNESS aparecerán listadas en este historial.</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebar.js"></script>
</body>
</html>
