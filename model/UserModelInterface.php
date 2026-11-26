<?php
/**
 * Interfaz para el Modelo de Usuarios.
 * Principio ISP (Interface Segregation): Define el contrato estricto de operaciones de usuario.
 * Principio DIP (Dependency Inversion): Los controladores dependen de esta interfaz y no de la clase concreta.
 */
interface UserModelInterface {
    public function getUsers(): array;
    public function getUserById(int $id): ?array;
    public function getUserByEmail(string $email): ?array;
    public function existeCorreo(string $correo, ?int $excluirId = null): bool;
    public function crear(string $nombre, string $correo, string $password): bool;
    public function actualizar(int $id, string $nombre, string $correo, ?string $password = null): bool;
    public function eliminar(int $id): bool;
    public function restablecerClave(string $email, string $nuevaClave): bool;
}
