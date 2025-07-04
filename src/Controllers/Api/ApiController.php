<?php

namespace App\Controllers\Api;

use App\Core\Attributes\Route;

class ApiController
{
    #[Route('/api', method: 'GET')]
    public function index(): string
    {
        return json_encode(['message' => 'Welcome to the API']);
    }

    #[Route('/api/users', method: 'GET')]
    public function getUsers(): string
    {
        return json_encode(['users' => ['User1', 'User2', 'User3']]);
    }

    #[Route('/api/users/{id}', method: 'GET')]
    public function getUser(string $id): string
    {
        return json_encode(['user' => ['id' => $id, 'name' => 'User' . $id]]);
    }

    #[Route('/api/users', method: 'POST')]
    public function createUser(): string
    {
        return json_encode(['message' => 'User created successfully']);
    }

    #[Route('/api/users/{id}', method: 'PUT')]
    public function updateUser(string $id): string
    {
        return json_encode(['message' => "User with ID $id updated successfully"]);
    }

    #[Route('/api/users/{id}', method: 'DELETE')]
    public function deleteUser(string $id): string
    {
        return json_encode(['message' => "User with ID $id deleted successfully"]);
    }
}
