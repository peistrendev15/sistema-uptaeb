<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - PRIME FITNESS</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Estilos Unificados -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="auth-bg">

<div class="auth-card">
    <!-- Encabezado -->
    <div class="auth-header">
        <div class="logo-badge" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
            <i class="bi bi-key-fill"></i>
        </div>
        <h4 class="fw-bold mb-1">Recuperar Contraseña</h4>
        <p class="text-white-50 small mb-0">Restaura el acceso a tu cuenta</p>
    </div>

    <!-- Formulario -->
    <div class="p-4 p-sm-5 bg-white">
        <form id="recoveryForm" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary small">CORREO ELECTRÓNICO REGISTRADO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control bg-light border-start-0" id="email" placeholder="correo@ejemplo.com" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="nuevaClave" class="form-label fw-semibold text-secondary small">NUEVA CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="nuevaClave" placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirmarClave" class="form-label fw-semibold text-secondary small">CONFIRMAR NUEVA CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="confirmarClave" placeholder="Repite la contraseña" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-warning py-2 fw-bold text-dark shadow-sm rounded-3" id="btnRestablecer">
                    <i class="bi bi-arrow-clockwise me-1"></i> Restablecer Contraseña
                </button>
            </div>

            <div class="text-center pt-3 border-top">
                <a href="index.php?action=login" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Iniciar Sesión
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script AJAX de Recuperación -->
<script src="assets/js/recuperar.js"></script>
</body>
</html>
