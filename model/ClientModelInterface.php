<?php
/**
 * Interfaz para el Modelo de Clientes.
 * Principio ISP y DIP (SOLID): Define las operaciones completas del módulo de Clientes.
 */
interface ClientModelInterface {
    public function getClients(): array;
    public function countClients(): int;
    public function getClientById(int $id): ?array;
    public function getClientByEmail(string $email): ?array;
    public function existeCorreo(string $correo, ?int $excluirId = null): bool;
    public function crear(string $nombre, string $correo, string $password): bool;
    public function actualizar(int $id, string $nombre, string $correo, ?string $password = null): bool;
    public function eliminar(int $id): bool;
}
