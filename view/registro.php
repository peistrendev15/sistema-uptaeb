<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - PRIME FITNESS</title>
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
        <div class="logo-badge">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <h4 class="fw-bold mb-1">Crear Cuenta</h4>
        <p class="text-white-50 small mb-0">Únete al sistema de gestión</p>
    </div>

    <!-- Formulario -->
    <div class="p-4 p-sm-5 bg-white">
        <form id="registerForm" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-secondary small">NOMBRE COMPLETO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" id="name" placeholder="Ej: María Pérez" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary small">CORREO ELECTRÓNICO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control bg-light border-start-0" id="email" placeholder="correo@ejemplo.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-secondary small">CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="password" placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirmPassword" class="form-label fw-semibold text-secondary small">CONFIRMAR CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="confirmPassword" placeholder="Repite tu contraseña" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary-custom py-2 shadow-sm" id="btnRegister">
                    <i class="bi bi-check-circle me-1"></i> Registrarse
                </button>
            </div>

            <div class="text-center pt-3 border-top">
                <span class="text-muted small">¿Ya tienes una cuenta? </span>
                <a href="index.php?action=login" class="text-decoration-none fw-semibold small" style="color: #57b982;">
                    Iniciar Sesión
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script AJAX de Registro -->
<script src="assets/js/registro.js"></script>
</body>
</html>
