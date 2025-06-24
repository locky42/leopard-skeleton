<?php

namespace App\Controllers;

use App\Core\Attributes\Route;

class HomeController
{
    #[Route('/', method: 'GET')]
    public function index(): void
    {
        echo "Hello from HomeController::" . __FUNCTION__;  
    }

    public function contact(): void
    {
        echo "Hello from HomeController::contact";
    }
}
