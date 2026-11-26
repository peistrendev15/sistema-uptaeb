<?php
/**
 * Front Controller & Enrutador Principal (MVC) - PRIME FITNESS
 * 
 * Actúa como Composition Root:
 * 1. Carga el Autoload de Composer (vendor/autoload.php).
 * 2. Inicializa la conexión PDO.
 * 3. Instancia los Modelos pasándole la conexión.
 * 4. Instancia los Controladores inyectándole los Modelos.
 * 5. Despacha la acción solicitada.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/model/ClientModelInterface.php';
require_once __DIR__ . '/model/clientModel.php';
require_once __DIR__ . '/controller/ClientController.php';

// ==========================================================
// COMPOSITION ROOT (Inyección de Dependencias - SOLID DIP)
// ==========================================================
$db = Database::getConnection();
$userModel = new UserModel($db);
$clientModel = new ClientModel($db);

$controller = new UserController($userModel, $clientModel);
$clientController = new ClientController($clientModel);

// Acción solicitada por GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? 'login';

switch ($action) {
    // ==========================================
    // 1. RUTAS DE VISTAS ADMINISTRATIVAS (HTML)
    // ==========================================
    case 'login':
        $controller->showLogin();
        break;

    case 'registro':
        $controller->showRegistro();
        break;

    case 'recuperar':
        $controller->showRecuperar();
        break;

    // Módulo 1: Inicio (Dashboard Overview con analíticas reales)
    case 'inicio':
    case 'dashboard':
        $controller->showInicio();
        break;

    // Módulo 2: Admin (Gestión de Usuarios Admin con Side Modal y AJAX)
    case 'admin':
    case 'usuarios':
        $controller->showAdmin();
        break;

    // Módulo 3: Clientes (Gestión de Clientes en el Dashboard Admin con Side Modal y AJAX)
    case 'clientes':
        $clientController->showAdminClientes();
        break;

    // ==========================================
    // 2. RUTAS DEL PORTAL DE CLIENTES Y CRUD CLIENTES (AJAX)
    // ==========================================
    case 'cliente_login':
        $clientController->showLogin();
        break;

    case 'cliente_login_auth':
        $clientController->login();
        break;

    case 'cliente_dashboard':
        $clientController->showDashboard();
        break;

    case 'cliente_listar':
        $clientController->listar();
        break;

    case 'cliente_obtener':
        $clientController->obtener();
        break;

    case 'cliente_registrar':
        $clientController->registrar();
        break;

    case 'cliente_actualizar':
        $clientController->actualizar();
        break;

    case 'cliente_eliminar':
        $clientController->eliminar();
        break;

    case 'cliente_logout':
        $clientController->logout();
        break;

    // ==========================================
    // 3. RUTAS DE AUTENTICACIÓN ADMIN
    // ==========================================
    case 'login_auth':
        $controller->login();
        break;

    case 'logout':
        $controller->logout();
        break;

    // ==========================================
    // 4. RUTA DE RECUPERACIÓN PRIMITIVA (AJAX)
    // ==========================================
    case 'restablecer_clave':
        $controller->restablecerClave();
        break;

    // ==========================================
    // 5. RUTAS DEL CRUD Y REGISTRO ADMIN (AJAX)
    // ==========================================
    case 'listar':
        $controller->listar();
        break;

    case 'obtener':
        $controller->obtener();
        break;

    case 'registrar':
    case 'registrar_publico':
        $controller->registrar();
        break;

    case 'actualizar':
        $controller->actualizar();
        break;

    case 'eliminar':
        $controller->eliminar();
        break;

    default:
        $controller->showLogin();
        break;
}
