<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRIME FITNESS - Portal de Clientes</title>
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
            border-color: #6bd098;
            box-shadow: 0 0 0 3px rgba(107, 208, 152, 0.2);
            outline: none;
        }

        .btn-submit-client {
            background-color: #6bd098;
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

        .btn-submit-client:hover {
            background-color: #57b982;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(107, 208, 152, 0.35);
        }

        /* Columna Derecha: UNA SOLA IMAGEN FIJA (Sin transición) */
        .split-image-col {
            flex: 1;
            position: relative;
            max-width: 50%;
            overflow: hidden;
            background-color: #0f172a;
            background-image: url('assets/img/login1.jfif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.15) 0%, rgba(15, 23, 42, 0.85) 100%);
            z-index: 2;
        }

        .split-image-content {
            position: absolute;
            bottom: 45px;
            left: 45px;
            right: 45px;
            z-index: 3;
            color: #ffffff;
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
            margin-top: 30px;
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
    
    <!-- COLUMNA IZQUIERDA: Formulario de Acceso de Clientes -->
    <div class="split-form-col">
        <div class="split-form-wrapper">
            
            <div class="mb-2">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill fw-semibold small">
                    <i class="bi bi-person-badge me-1"></i> Área de Clientes
                </span>
            </div>
            <h2 class="title">Portal de Clientes</h2>
            <p class="subtitle">PRIME FITNESS · Consulta tus pedidos</p>

            <form id="clientLoginForm" novalidate>
                <!-- Correo Electrónico -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Correo electrónico <span class="required-star">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" placeholder="cliente@correo.com" required autofocus>
                </div>

                <!-- Contraseña con botón de ojo -->
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Mostrar/Ocultar contraseña">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Botón de Ingreso -->
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-submit-client" id="btnClientLogin">
                        <span>Ingresar como Cliente</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Enlace para ir al Login Administrativo -->
                <div class="text-center mt-4 pt-3 border-top">
                    <a href="index.php?action=login" class="text-decoration-none small text-muted">
                        <i class="bi bi-shield-lock me-1"></i> Ir al Panel Administrativo
                    </a>
                </div>

                <!-- Demo info -->
                <div class="text-center text-muted small mt-3 p-2 bg-light rounded-3">
                    <span>Cliente Demo: <code>cliente@correo.com</code> | <code>123456</code></span>
                </div>

                <div class="version-tag">
                    v1.0.0 (PRIME FITNESS)
                </div>
            </form>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Una Sola Imagen Fija (Sin Transición) -->
    <div class="split-image-col">
        <div class="image-overlay"></div>
        <div class="split-image-content">
            <h2>PRIME FITNESS</h2>
            <p>Gestiona y consulta el historial de tus solicitudes y pedidos en línea.</p>
        </div>
    </div>

</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script AJAX de Login de Clientes -->
<script src="assets/js/cliente_login.js"></script>

<script>
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
