<?php
require_once __DIR__ . '/../model/UserModelInterface.php';
require_once __DIR__ . '/../model/ClientModelInterface.php';

/**
 * Controlador de Usuarios (Login, Registro Público, Recuperación, Inicio y Admin)
 * 
 * Principio SRP: Orquesta solicitudes HTTP, valida entradas y responde en JSON para AJAX.
 * Principio DIP: NO instancia directamente clases con 'new'. Recibe UserModelInterface y ClientModelInterface inyectadas desde afuera.
 */
class UserController {
    private UserModelInterface $model;
    private ?ClientModelInterface $clientModel;

    /**
     * Inyección de Dependencias
     */
    public function __construct(UserModelInterface $model, ?ClientModelInterface $clientModel = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = $model;
        $this->clientModel = $clientModel;
    }

    // ==========================================
    // 1. VISTAS DEL SISTEMA
    // ==========================================

    /** Vista 1: Login de Usuario (Estilo Paper Dashboard) */
    public function showLogin(): void {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=inicio');
            exit;
        }
        require_once __DIR__ . '/../view/login.php';
    }

    /** Vista 2: Registro Público de Cuenta */
    public function showRegistro(): void {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=inicio');
            exit;
        }
        require_once __DIR__ . '/../view/registro.php';
    }

    /** Vista 3: Recuperación Primitiva de Contraseña */
    public function showRecuperar(): void {
        require_once __DIR__ . '/../view/recuperar.php';
    }

    /** Vista 4: MÓDULO 1 - Inicio / Overview (Dashboard con métricas) */
    public function showInicio(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $usuarios = $this->model->getUsers();
        $totalUsuarios = count($usuarios);
        $totalClientes = $this->clientModel ? $this->clientModel->countClients() : 0;
        $totalPedidos = 0;
        $ingresosMes = 0;
        $ingresosTotales = 0;

        require_once __DIR__ . '/../view/inicio.php';
    }

    /** Vista 5: MÓDULO 2 - Admin (Gestión de Usuarios con Modal y AJAX) */
    public function showAdmin(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../view/usuarios.php';
    }

    // ==========================================
    // 2. AUTENTICACIÓN (LOGIN & LOGOUT)
    // ==========================================

    /** AJAX POST: Iniciar sesión */
    public function login(): void {
        $data = $this->getInput();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->json(422, false, 'Debes ingresar tu correo y contraseña.');
            return;
        }

        $user = $this->model->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->json(401, false, 'Correo o contraseña incorrectos.');
            return;
        }

        // Crear sesión de usuario
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email']
        ];

        $this->json(200, true, '¡Bienvenido! Redirigiendo al panel...', ['user' => $_SESSION['user']]);
    }

    /** GET: Cerrar sesión */
    public function logout(): void {
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
    }

    // ==========================================
    // 3. RECUPERACIÓN PRIMITIVA DE CONTRASEÑA
    // ==========================================

    /** AJAX POST: Restablecer contraseña con Correo, Nueva Contraseña y Confirmación */
    public function restablecerClave(): void {
        $data = $this->getInput();

        $email = trim($data['email'] ?? '');
        $nuevaClave = $data['nueva_clave'] ?? '';
        $confirmarClave = $data['confirmar_clave'] ?? '';

        if (empty($email) || empty($nuevaClave) || empty($confirmarClave)) {
            $this->json(422, false, 'Todos los campos son obligatorios.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(422, false, 'Ingresa un formato de correo electrónico válido.');
            return;
        }

        if (strlen($nuevaClave) < 6) {
            $this->json(422, false, 'La contraseña debe tener al menos 6 caracteres.');
            return;
        }

        if ($nuevaClave !== $confirmarClave) {
            $this->json(422, false, 'Las contraseñas no coinciden. Por favor verifícalas.');
            return;
        }

        $user = $this->model->getUserByEmail($email);
        if (!$user) {
            $this->json(404, false, 'No existe ningún usuario registrado con ese correo.');
            return;
        }

        $ok = $this->model->restablecerClave($email, $nuevaClave);
        if ($ok) {
            $this->json(200, true, '¡Contraseña restablecida con éxito! Redirigiendo al inicio de sesión...');
        } else {
            $this->json(500, false, 'Hubo un error al actualizar la contraseña en el servidor.');
        }
    }

    // ==========================================
    // 4. CRUD Y REGISTRO DE USUARIOS (AJAX)
    // ==========================================

    /** AJAX GET: Listar usuarios */
    public function listar(): void {
        try {
            $usuarios = $this->model->getUsers();
            $this->json(200, true, 'Lista de usuarios obtenida', ['usuarios' => $usuarios]);
        } catch (Throwable $e) {
            $this->json(500, false, 'Error al listar usuarios: ' . $e->getMessage());
        }
    }

    /** AJAX GET: Obtener usuario por ID */
    public function obtener(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->json(400, false, 'ID inválido.');
            return;
        }

        $usuario = $this->model->getUserById($id);
        if (!$usuario) {
            $this->json(404, false, 'Usuario no encontrado.');
            return;
        }

        $this->json(200, true, 'Usuario obtenido', ['usuario' => $usuario]);
    }

    /** AJAX POST: Registrar nuevo usuario (Panel o Público) */
    public function registrar(): void {
        $data = $this->getInput();

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $this->json(422, false, 'Nombre, correo y contraseña son obligatorios.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(422, false, 'El correo electrónico no es válido.');
            return;
        }

        if (strlen($password) < 6) {
            $this->json(422, false, 'La contraseña debe tener al menos 6 caracteres.');
            return;
        }

        if ($this->model->existeCorreo($email)) {
            $this->json(409, false, 'El correo electrónico ya está registrado.');
            return;
        }

        $creado = $this->model->crear($name, $email, $password);
        if ($creado) {
            $this->json(201, true, '¡Usuario registrado correctamente!');
        } else {
            $this->json(500, false, 'No se pudo registrar el usuario.');
        }
    }

    /** AJAX POST: Actualizar usuario */
    public function actualizar(): void {
        $data = $this->getInput();

        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = !empty($data['password']) ? $data['password'] : null;

        if ($id <= 0 || empty($name) || empty($email)) {
            $this->json(422, false, 'Datos insuficientes para actualizar.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(422, false, 'El correo electrónico no es válido.');
            return;
        }

        if ($this->model->existeCorreo($email, $id)) {
            $this->json(409, false, 'El correo ya pertenece a otro usuario.');
            return;
        }

        $actualizado = $this->model->actualizar($id, $name, $email, $password);
        if ($actualizado) {
            $this->json(200, true, '¡Usuario actualizado exitosamente!');
        } else {
            $this->json(500, false, 'No se pudo actualizar el usuario.');
        }
    }

    /** AJAX POST: Eliminar usuario */
    public function eliminar(): void {
        $data = $this->getInput();
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->json(400, false, 'ID inválido para eliminar.');
            return;
        }

        $eliminado = $this->model->eliminar($id);
        if ($eliminado) {
            $this->json(200, true, '¡Usuario eliminado exitosamente!');
        } else {
            $this->json(500, false, 'No se pudo eliminar el usuario.');
        }
    }

    /** Utilidad: Lee JSON crudo o $_POST */
    private function getInput(): array {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        return is_array($json) ? $json : $_POST;
    }

    /** Utilidad: Responde en JSON estandarizado */
    private function json(int $status, bool $success, string $message, array $extra = []): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $extra));
        exit;
    }
}
