<?php

namespace App\Controllers\Admin;

use Leopard\Core\Attributes\Route;

class DashboardController
{
    #[Route('/admin', method: 'GET')]
    public function index(): string
    {
        return "Welcome to the Admin Dashboard!";
    }

    #[Route('/admin/dashboard/stats', method: 'GET')]
    public function stats(): string
    {
        return "Here are the dashboard statistics.";
    }

    #[Route('/admin/dashboard/settings', method: 'POST')]
    public function updateSettings(): string
    {
        return "Dashboard settings updated successfully.";
    }
}
