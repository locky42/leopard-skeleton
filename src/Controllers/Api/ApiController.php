<?php

namespace App\Controllers\Api;

use Leopard\Core\Attributes\Route;

/**
 * ApiController class extends the base ApiController from the application's core.
 * 
 * This class serves as a controller for handling API-related requests within the 
 * Leopard Skeleton project. It inherits functionality from the core ApiController 
 * and can be customized to implement specific API logic as needed.
 * 
 */
class ApiController extends \Leopard\Core\Controllers\ApiController
{
    #[Route('/api', method: 'GET')]
    public function index(): string
    {
        return $this->formatResponse(['message' => 'Welcome to the API']);
    }

    #[Route('/api/users', method: 'GET')]
    public function getUsers(): string
    {
        return $this->formatResponse(['users' => ['User1', 'User2', 'User3']]);
    }

    #[Route('/api/users/{id}', method: 'GET')]
    public function getUser(string $id): string
    {
        return $this->formatResponse(['user' => ['id' => $id, 'name' => 'User' . $id]]);
    }

    #[Route('/api/users', method: 'POST')]
    public function createUser(): string
    {
        return $this->formatResponse(['message' => 'User created successfully']);
    }

    #[Route('/api/users/{id}', method: 'PUT')]
    public function updateUser(string $id): string
    {
        return $this->formatResponse(['message' => "User with ID $id updated successfully"]);
    }

    #[Route('/api/users/{id}', method: 'DELETE')]
    public function deleteUser(string $id): string
    {
        return $this->formatResponse(['message' => "User with ID $id deleted successfully"]);
    }
}
