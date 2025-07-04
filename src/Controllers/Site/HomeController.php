<?php

namespace App\Controllers\Site;

use App\Core\Attributes\Route;

class HomeController
{
    #[Route('/', method: 'GET')]
    public function index(): string
    {
        return "Hello from HomeController::" . __FUNCTION__;  
    }

    public function contact(): string
    {
        return "Hello from HomeController::contact";
    }
}
