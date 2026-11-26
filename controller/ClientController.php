<?php
require_once __DIR__ . '/../model/ClientModelInterface.php';

/**
 * Controlador de Clientes (Portal del Cliente + CRUD Administrativo de Clientes)
 * 
 * Principio SRP: Gestiona el flujo y CRUD de los clientes.
 * Principio DIP: Depende de la abstracción ClientModelInterface inyectada por constructor.
 */
class ClientController {
    private ClientModelInterface $clientModel;

    public function __construct(ClientModelInterface $clientModel) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->clientModel = $clientModel;
    }

    // ==========================================
    // 1. PORTAL PÚBLICO / DEL CLIENTE
    // ==========================================

    /** Vista 1: Login de Clientes (1 sola imagen fija sin transición) */
    public function showLogin(): void {
        if (isset($_SESSION['client'])) {
            header('Location: index.php?action=cliente_dashboard');
            exit;
        }
        require_once __DIR__ . '/../view/cliente_login.php';
    }

    /** Vista 2: Dashboard del Cliente (Muestra cantidad de pedidos solicitados e historial en 0) */
    public function showDashboard(): void {
        if (!isset($_SESSION['client'])) {
            header('Location: index.php?action=cliente_login');
            exit;
        }

        $pedidosSolicitados = 0;
        $historialPedidos = [];

        require_once __DIR__ . '/../view/cliente_dashboard.php';
    }

    /** AJAX POST: Autenticación de Clientes */
    public function login(): void {
        $data = $this->getInput();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->json(422, false, 'Debes ingresar tu correo y contraseña.');
            return;
        }

        $client = $this->clientModel->getClientByEmail($email);
        if (!$client || !password_verify($password, $client['password'])) {
            $this->json(401, false, 'Correo o contraseña de cliente incorrectos.');
            return;
        }

        $_SESSION['client'] = [
            'id'         => $client['id'],
            'name'       => $client['name'],
            'email'      => $client['email'],
            'created_at' => $client['created_at']
        ];

        $this->json(200, true, '¡Bienvenido a PRIME FITNESS!', ['client' => $_SESSION['client']]);
    }

    /** GET: Cerrar sesión de cliente */
    public function logout(): void {
        unset($_SESSION['client']);
        header('Location: index.php?action=cliente_login');
        exit;
    }

    // ==========================================
    // 2. MÓDULO DE CLIENTES EN EL PANEL ADMIN
    // ==========================================

    /** Vista 3: Módulo de Gestión de Clientes en el Dashboard Admin */
    public function showAdminClientes(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../view/clientes.php';
    }

    /** AJAX GET: Listar todos los clientes */
    public function listar(): void {
        try {
            $clientes = $this->clientModel->getClients();
            $this->json(200, true, 'Lista de clientes obtenida', ['clientes' => $clientes]);
        } catch (Throwable $e) {
            $this->json(500, false, 'Error al listar clientes: ' . $e->getMessage());
        }
    }

    /** AJAX GET: Obtener un cliente por ID */
    public function obtener(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->json(400, false, 'ID de cliente inválido.');
            return;
        }

        $cliente = $this->clientModel->getClientById($id);
        if (!$cliente) {
            $this->json(404, false, 'Cliente no encontrado.');
            return;
        }

        $this->json(200, true, 'Cliente obtenido', ['cliente' => $cliente]);
    }

    /** AJAX POST: Registrar nuevo cliente */
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
            $this->json(422, false, 'La contraseña debe tener mínimo 6 caracteres.');
            return;
        }

        if ($this->clientModel->existeCorreo($email)) {
            $this->json(409, false, 'Este correo ya está registrado como cliente.');
            return;
        }

        $ok = $this->clientModel->crear($name, $email, $password);
        if ($ok) {
            $this->json(201, true, '¡Cliente registrado exitosamente!');
        } else {
            $this->json(500, false, 'No se pudo registrar el cliente.');
        }
    }

    /** AJAX POST: Actualizar cliente */
    public function actualizar(): void {
        $data = $this->getInput();

        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = !empty($data['password']) ? $data['password'] : null;

        if ($id <= 0 || empty($name) || empty($email)) {
            $this->json(422, false, 'Datos insuficientes para actualizar el cliente.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(422, false, 'El correo electrónico no es válido.');
            return;
        }

        if ($this->clientModel->existeCorreo($email, $id)) {
            $this->json(409, false, 'El correo ya pertenece a otro cliente.');
            return;
        }

        $actualizado = $this->clientModel->actualizar($id, $name, $email, $password);
        if ($actualizado) {
            $this->json(200, true, '¡Cliente actualizado exitosamente!');
        } else {
            $this->json(500, false, 'No se pudo actualizar el cliente.');
        }
    }

    /** AJAX POST: Eliminar cliente */
    public function eliminar(): void {
        $data = $this->getInput();
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->json(400, false, 'ID inválido para eliminar.');
            return;
        }

        $eliminado = $this->clientModel->eliminar($id);
        if ($eliminado) {
            $this->json(200, true, '¡Cliente eliminado exitosamente!');
        } else {
            $this->json(500, false, 'No se pudo eliminar el cliente.');
        }
    }

    private function getInput(): array {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        return is_array($json) ? $json : $_POST;
    }

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
