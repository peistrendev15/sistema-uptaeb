<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRIME FITNESS - Iniciar Sesión</title>
    <!-- Bootstrap 5 CSS e Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Estilos Unificados -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body.split-login-body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }

        .split-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Columna Izquierda: Formulario */
        .split-form-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 30px;
            background-color: #ffffff;
            max-width: 50%;
            z-index: 10;
        }

        .split-form-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .split-form-wrapper .title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .split-form-wrapper .subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        .split-form-wrapper .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .split-form-wrapper .form-label .required-star {
            color: #ef4444;
        }

        .split-form-wrapper .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.95rem;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .split-form-wrapper .form-control:focus {
            border-color: #64748b;
            box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.15);
            outline: none;
        }

        .btn-submit-login {
            background-color: #64748b;
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-login:hover {
            background-color: #475569;
            color: #ffffff;
        }

        /* Columna Derecha: Contenedor Carrusel */
        .split-image-col {
            flex: 1;
            position: relative;
            max-width: 50%;
            overflow: hidden;
            background-color: #0f172a;
        }

        /* IMÁGENES CON CSS PURO KEYFRAMES (100% GARANTIZADO) */
        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            animation: sliderAnimation 12s infinite ease-in-out;
        }

        .slide-1 {
            background-image: url('assets/img/login1.jfif');
            animation-delay: 0s;
        }

        .slide-2 {
            background-image: url('assets/img/login2.jfif');
            animation-delay: 4s;
        }

        .slide-3 {
            background-image: url('assets/img/login3.jfif');
            animation-delay: 8s;
        }

        /* Cada imagen dura 4s visible de un total de 12s */
        @keyframes sliderAnimation {
            0% {
                opacity: 0;
                transform: scale(1.06);
            }
            8% {
                opacity: 1;
                transform: scale(1.02);
            }
            33% {
                opacity: 1;
                transform: scale(1);
            }
            41% {
                opacity: 0;
                transform: scale(0.98);
            }
            100% {
                opacity: 0;
                transform: scale(1.06);
            }
        }

        /* Capa oscura degradada */
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.15) 0%, rgba(15, 23, 42, 0.8) 100%);
            z-index: 5;
            pointer-events: none;
        }

        /* Textos sobre la imagen */
        .split-image-content {
            position: absolute;
            bottom: 45px;
            left: 45px;
            right: 45px;
            z-index: 6;
            color: #ffffff;
            pointer-events: none;
        }

        .split-image-content h2 {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        }

        .split-image-content p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 0;
            text-shadow: 0 1px 4px rgba(0,0,0,0.7);
        }

        .version-tag {
            color: #94a3b8;
            font-size: 0.8rem;
            text-align: center;
            margin-top: 35px;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 1.1rem;
        }

        @media (max-width: 991px) {
            .split-container {
                flex-direction: column-reverse;
            }
            .split-form-col {
                max-width: 100%;
                flex: auto;
                padding: 40px 20px;
            }
            .split-image-col {
                max-width: 100%;
                min-height: 280px;
                flex: auto;
            }
            .split-image-content {
                bottom: 25px;
                left: 25px;
                right: 25px;
            }
            .split-image-content h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="split-login-body">

<div class="split-container">
    
    <!-- COLUMNA IZQUIERDA: Formulario de Acceso -->
    <div class="split-form-col">
        <div class="split-form-wrapper">
            
            <h2 class="title">Panel Administrativo</h2>
            <p class="subtitle">PRIME FITNESS · Acceso restringido</p>

            <form id="loginForm" novalidate>
                <!-- Correo Electrónico -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Correo electrónico <span class="required-star">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" placeholder="usuario@correo.com" required autofocus>
                </div>

                <!-- Contraseña con botón de ojo para ver/ocultar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Contraseña</label>
                        <a href="index.php?action=recuperar" class="small text-decoration-none text-muted" style="font-size: 0.8rem;">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Mostrar/Ocultar contraseña">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Botón de Ingreso Principal -->
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-submit-login" id="btnLogin">
                        <span>Ingresar</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Enlace a Registro Público y Portal de Clientes -->
                <div class="text-center mt-4 pt-2">
                    <span class="text-muted small">¿No tienes una cuenta? </span>
                    <a href="index.php?action=registro" class="text-decoration-none small fw-semibold text-secondary">
                        Regístrate aquí
                    </a>
                </div>
                <div class="text-center mt-2">
                    <a href="index.php?action=cliente_login" class="text-decoration-none small text-success fw-semibold">
                        <i class="bi bi-person-badge me-1"></i> Ir al Portal de Clientes
                    </a>
                </div>

                <!-- Versión al pie del formulario -->
                <div class="version-tag">
                    v1.0.0 (PRIME FITNESS)
                </div>
            </form>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Carrusel Automático con CSS Keyframes Puro -->
    <div class="split-image-col">
        <div class="carousel-slide slide-1"></div>
        <div class="carousel-slide slide-2"></div>
        <div class="carousel-slide slide-3"></div>

        <!-- Capa oscura de contraste -->
        <div class="image-overlay"></div>

        <!-- Textos sobre la imagen -->
        <div class="split-image-content">
            <h2>PRIME FITNESS</h2>
            <p>Administración y monitoreo de usuarios del sistema.</p>
        </div>
    </div>

</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script AJAX de Login -->
<script src="assets/js/login.js"></script>

<script>
    // Conmutador de visibilidad de contraseña (ojo)
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (togglePasswordBtn && passwordInput && toggleIcon) {
        togglePasswordBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
</script>

</body>
</html>
