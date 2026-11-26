<?php
require_once __DIR__ . '/ClientModelInterface.php';

/**
 * Modelo de Clientes con PDO.
 * Principio SRP: Acceso exclusivo a la tabla 'clientes'.
 * Principio DIP: Recibe la instancia PDO por inyección de dependencias en el constructor.
 */
class ClientModel implements ClientModelInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getClients(): array {
        $sql = "SELECT id, name, email, created_at FROM clientes ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countClients(): int {
        $sql = "SELECT COUNT(*) as total FROM clientes";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function getClientById(int $id): ?array {
        $sql = "SELECT id, name, email, created_at FROM clientes WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getClientByEmail(string $email): ?array {
        $sql = "SELECT id, name, email, password, created_at FROM clientes WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => trim(strtolower($email))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function existeCorreo(string $correo, ?int $excluirId = null): bool {
        if ($excluirId) {
            $sql = "SELECT id FROM clientes WHERE email = :email AND id != :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => trim(strtolower($correo)), ':id' => $excluirId]);
        } else {
            $sql = "SELECT id FROM clientes WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => trim(strtolower($correo))]);
        }
        return (bool)$stmt->fetch();
    }

    public function crear(string $nombre, string $correo, string $password): bool {
        $sql = "INSERT INTO clientes (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->db->prepare($sql);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        return $stmt->execute([
            ':name'     => trim($nombre),
            ':email'    => trim(strtolower($correo)),
            ':password' => $passwordHash
        ]);
    }

    public function actualizar(int $id, string $nombre, string $correo, ?string $password = null): bool {
        if (!empty($password)) {
            $sql = "UPDATE clientes SET name = :name, email = :email, password = :password WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'     => trim($nombre),
                ':email'    => trim(strtolower($correo)),
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':id'       => $id
            ]);
        } else {
            $sql = "UPDATE clientes SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'  => trim($nombre),
                ':email' => trim(strtolower($correo)),
                ':id'    => $id
            ]);
        }
    }

    public function eliminar(int $id): bool {
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
