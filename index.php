<?php
/**
 * Enrutador Principal (Front Controller) - PRIME FITNESS
 * 
 * 1. Carga el Autoload de Composer (que carga las clases de config/ y model/).
 * 2. Redirige la petición al archivo controlador correspondiente (donde se instancian los modelos).
 */

require_once __DIR__ . '/vendor/autoload.php';

// Acción solicitada por GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? 'login';

// Rutas que pertenecen al Controlador de Clientes
$clientActions = [
    'clientes',
    'cliente_login',
    'cliente_login_auth',
    'cliente_dashboard',
    'cliente_listar',
    'cliente_obtener',
    'cliente_registrar',
    'cliente_actualizar',
    'cliente_eliminar',
    'cliente_logout'
];

if (in_array($action, $clientActions, true)) {
    require_once __DIR__ . '/controller/ClientController.php';
} else {
    require_once __DIR__ . '/controller/UserController.php';
}
