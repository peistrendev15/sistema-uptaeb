<?php
/**
 * Controlador de Usuarios (PRIME FITNESS)
 * 
 * En este archivo NO se declara ninguna clase.
 * Únicamente se instancian las clases del Modelo, se reciben las peticiones
 * y se llaman a los métodos de los objetos instanciados para cargar las vistas o responder a AJAX.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. IMPORTACIÓN DE LA BASE DE DATOS Y LOS MODELOS
require_once __DIR__ . '/../config/dataBase.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../model/clientModel.php';

// 2. INSTANCIACIÓN DE LAS CLASES DEL MODELO
$db = Database::getConnection();
$userModel = new UserModel($db);
$clientModel = new ClientModel($db);

// Lectura de datos enviados por AJAX (JSON) o formularios ($_POST)
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$data = is_array($jsonInput) ? $jsonInput : $_POST;

// Acción solicitada
$action = $_GET['action'] ?? $_POST['action'] ?? 'login';

// 2. PROCESAMIENTO DE LA ACCIÓN USANDO LA INSTANCIA ($userModel)
switch ($action) {

    // ==========================================
    // VISTAS DEL SISTEMA
    // ==========================================
    case 'login':
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=inicio');
            exit;
        }
        require_once __DIR__ . '/../view/login.php';
        break;

    case 'registro':
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=inicio');
            exit;
        }
        require_once __DIR__ . '/../view/registro.php';
        break;

    case 'recuperar':
        require_once __DIR__ . '/../view/recuperar.php';
        break;

    case 'inicio':
    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        // Usamos las instancias para consultar las métricas
        $usuarios = $userModel->getUsers();
        $totalUsuarios = count($usuarios);
        $totalClientes = $clientModel->countClients();
        $totalPedidos = 0;
        $ingresosMes = 0;
        $ingresosTotales = 0;

        require_once __DIR__ . '/../view/inicio.php';
        break;

    case 'admin':
    case 'usuarios':
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../view/usuarios.php';
        break;

    // ==========================================
    // AUTENTICACIÓN (LOGIN & LOGOUT)
    // ==========================================
    case 'login_auth':
        header('Content-Type: application/json; charset=UTF-8');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Debes ingresar tu correo y contraseña.']);
            exit;
        }

        $user = $userModel->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos.']);
            exit;
        }

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email']
        ];

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => '¡Bienvenido! Redirigiendo al panel...',
            'user'    => $_SESSION['user']
        ]);
        exit;

    case 'logout':
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: index.php?action=login');
        exit;

    // ==========================================
    // RECUPERACIÓN PRIMITIVA DE CONTRASEÑA
    // ==========================================
    case 'restablecer_clave':
        header('Content-Type: application/json; charset=UTF-8');
        $email = trim($data['email'] ?? '');
        $nuevaClave = $data['nueva_clave'] ?? '';
        $confirmarClave = $data['confirmar_clave'] ?? '';

        if (empty($email) || empty($nuevaClave) || empty($confirmarClave)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Ingresa un formato de correo electrónico válido.']);
            exit;
        }

        if (strlen($nuevaClave) < 6) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
            exit;
        }

        if ($nuevaClave !== $confirmarClave) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden. Por favor verifícalas.']);
            exit;
        }

        $user = $userModel->getUserByEmail($email);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No existe ningún usuario registrado con ese correo.']);
            exit;
        }

        $ok = $userModel->restablecerClave($email, $nuevaClave);
        if ($ok) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => '¡Contraseña restablecida con éxito! Redirigiendo al inicio de sesión...']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Hubo un error al actualizar la contraseña en el servidor.']);
        }
        exit;

    // ==========================================
    // CRUD Y REGISTRO DE USUARIOS (AJAX)
    // ==========================================
    case 'listar':
        header('Content-Type: application/json; charset=UTF-8');
        try {
            $usuarios = $userModel->getUsers();
            http_response_code(200);
            echo json_encode([
                'success'  => true,
                'message'  => 'Lista de usuarios obtenida',
                'usuarios' => $usuarios
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al listar usuarios: ' . $e->getMessage()]);
        }
        exit;

    case 'obtener':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit;
        }

        $usuario = $userModel->getUserById($id);
        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario obtenido',
            'usuario' => $usuario
        ]);
        exit;

    case 'registrar':
    case 'registrar_publico':
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
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
            exit;
        }

        if ($userModel->existeCorreo($email)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado.']);
            exit;
        }

        $creado = $userModel->crear($name, $email, $password);
        if ($creado) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => '¡Usuario registrado correctamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el usuario.']);
        }
        exit;

    case 'actualizar':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = !empty($data['password']) ? $data['password'] : null;

        if ($id <= 0 || empty($name) || empty($email)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Datos insuficientes para actualizar.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
            exit;
        }

        if ($userModel->existeCorreo($email, $id)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'El correo ya pertenece a otro usuario.']);
            exit;
        }

        $actualizado = $userModel->actualizar($id, $name, $email, $password);
        if ($actualizado) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => '¡Usuario actualizado exitosamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el usuario.']);
        }
        exit;

    case 'eliminar':
        header('Content-Type: application/json; charset=UTF-8');
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido para eliminar.']);
            exit;
        }

        $eliminado = $userModel->eliminar($id);
        if ($eliminado) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => '¡Usuario eliminado exitosamente!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el usuario.']);
        }
        exit;

    default:
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=inicio');
            exit;
        }
        require_once __DIR__ . '/../view/login.php';
        break;
}
