<?php
/**
 * Controlador de Clientes (PRIME FITNESS)
 * 
 * En este archivo NO se declara ninguna clase.
 * Únicamente se instancia la clase del Modelo (ClientModel), se reciben las peticiones
 * y se llaman a los métodos del objeto instanciado para cargar las vistas o responder a AJAX.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. IMPORTACIÓN DE LA BASE DE DATOS Y EL MODELO
require_once __DIR__ . '/../config/dataBase.php';
require_once __DIR__ . '/../model/clientModel.php';

// 2. INSTANCIACIÓN DE LA CLASE DEL MODELO
$db = Database::getConnection();
$clientModel = new ClientModel($db);

// Lectura de datos enviados por AJAX (JSON) o formularios ($_POST)
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$data = is_array($jsonInput) ? $jsonInput : $_POST;

// Acción solicitada
$action = $_GET['action'] ?? $_POST['action'] ?? 'cliente_login';

// 2. PROCESAMIENTO DE LA ACCIÓN USANDO LA INSTANCIA ($clientModel)
switch ($action) {

    // ==========================================
    // VISTAS DE CLIENTES
    // ==========================================
    case 'cliente_login':
        if (isset($_SESSION['client'])) {
            header('Location: index.php?action=cliente_dashboard');
            exit;
        }
        require_once __DIR__ . '/../view/cliente_login.php';
        break;

    case 'cliente_dashboard':
        if (!isset($_SESSION['client'])) {
            header('Location: index.php?action=cliente_login');
            exit;
        }
        $pedidosSolicitados = 0;
        $historialPedidos = [];
        require_once __DIR__ . '/../view/cliente_dashboard.php';
        break;

    case 'clientes':
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../view/clientes.php';
        break;

    // ==========================================
    // AUTENTICACIÓN DEL CLIENTE
    // ==========================================
    case 'cliente_login_auth':
        header('Content-Type: application/json; charset=UTF-8');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Debes ingresar tu correo y contraseña.']);
            exit;
        }

        $client = $clientModel->getClientByEmail($email);
        if (!$client || !password_verify($password, $client['password'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Correo o contraseña de cliente incorrectos.']);
            exit;
        }

        $_SESSION['client'] = [
            'id'         => $client['id'],
            'name'       => $client['name'],
            'email'      => $client['email'],
            'created_at' => $client['created_at']
        ];

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => '¡Bienvenido a PRIME FITNESS!',
            'client'  => $_SESSION['client']
        ]);
        exit;

    case 'cliente_logout':
        unset($_SESSION['client']);
        header('Location: index.php?action=cliente_login');
        exit;

    // ==========================================
    // CRUD ADMINISTRATIVO DE CLIENTES (AJAX)
    // ==========================================
    case 'cliente_listar':
        header('Content-Type: application/json; charset=UTF-8');
        try {
            $clientes = $clientModel->getClients();
            http_response_code(200);
            echo json_encode([
                'success'  => true,
                'message'  => 'Lista de clientes obtenida',
                'clientes' => $clientes
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al listar clientes: ' . $e->getMessage()]);
        }
        exit;

    case 'cliente_obtener':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de cliente inválido.']);
            exit;
        }

        $cliente = $clientModel->getClientById($id);
        if (!$cliente) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cliente obtenido',
            'cliente' => $cliente
        ]);
        exit;

    case 'cliente_registrar':
        header('Content-Type: application/json; charset=UTF-8');
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Nombre, correo y contraseña son obligatorios.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
            exit;
        }

        if (strlen($password) < 6) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener mínimo 6 caracteres.']);
            exit;
        }

        if ($clientModel->existeCorreo($email)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Este correo ya está registrado como cliente.']);
            exit;
        }

        $ok = $clientModel->crear($name, $email, $password);
        if ($ok) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => '¡Cliente registrado exitosamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el cliente.']);
        }
        exit;

    case 'cliente_actualizar':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = !empty($data['password']) ? $data['password'] : null;

        if ($id <= 0 || empty($name) || empty($email)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Datos insuficientes para actualizar el cliente.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
            exit;
        }

        if ($clientModel->existeCorreo($email, $id)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'El correo ya pertenece a otro cliente.']);
            exit;
        }

        $actualizado = $clientModel->actualizar($id, $name, $email, $password);
        if ($actualizado) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => '¡Cliente actualizado exitosamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el cliente.']);
        }
        exit;

    case 'cliente_eliminar':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido para eliminar.']);
            exit;
        }

        $eliminado = $clientModel->eliminar($id);
        if ($eliminado) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => '¡Cliente eliminado exitosamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el cliente.']);
        }
        exit;
}
