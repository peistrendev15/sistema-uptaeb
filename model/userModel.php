<?php
require_once __DIR__ . '/UserModelInterface.php';

/**
 * Modelo de Usuario con consultas PDO a MySQL.
 * Principio SRP: Se encarga exclusivamente del acceso a datos y consultas SQL.
 * Principio DIP: Recibe la conexión PDO por constructor (Inyección de Dependencias).
 */
class UserModel implements UserModelInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /** 1. READ: Obtener todos los usuarios */
    public function getUsers(): array {
        $sql = "SELECT id, name, email, created_at FROM users ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /** 2. READ: Obtener un usuario por ID */
    public function getUserById(int $id): ?array {
        $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Obtener un usuario por email */
    public function getUserByEmail(string $email): ?array {
        $sql = "SELECT id, name, email, password, created_at FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => trim(strtolower($email))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Verificar si un correo ya existe para evitar duplicados */
    public function existeCorreo(string $correo, ?int $excluirId = null): bool {
        if ($excluirId) {
            $sql = "SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => trim(strtolower($correo)), ':id' => $excluirId]);
        } else {
            $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => trim(strtolower($correo))]);
        }
        return (bool)$stmt->fetch();
    }

    /** 3. CREATE: Registrar un nuevo usuario */
    public function crear(string $nombre, string $correo, string $password): bool {
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->db->prepare($sql);

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        return $stmt->execute([
            ':name'     => trim($nombre),
            ':email'    => trim(strtolower($correo)),
            ':password' => $passwordHash
        ]);
    }

    /** 4. UPDATE: Actualizar datos de un usuario */
    public function actualizar(int $id, string $nombre, string $correo, ?string $password = null): bool {
        if (!empty($password)) {
            $sql = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'     => trim($nombre),
                ':email'    => trim(strtolower($correo)),
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':id'       => $id
            ]);
        } else {
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'  => trim($nombre),
                ':email' => trim(strtolower($correo)),
                ':id'    => $id
            ]);
        }
    }

    /** 5. DELETE: Eliminar un usuario */
    public function eliminar(int $id): bool {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /** 6. RECUPERACIÓN PRIMITIVA: Restablecer contraseña directamente por correo */
    public function restablecerClave(string $email, string $nuevaClave): bool {
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => password_hash($nuevaClave, PASSWORD_BCRYPT),
            ':email'    => trim(strtolower($email))
        ]);
    }
}
