<?php

namespace App\Controllers\Site;

use App\Core\Attributes\Route;

class HomeController extends \App\Core\Controllers\HtmlController
{
    #[Route('/', method: 'GET')]
    public function index(): string
    {
        return $this->view->render('home', ['title' => 'Home Page', 'message' => 'Welcome to the Home Page!']);
    }

    public function contact(): string
    {
        return "Hello from HomeController::contact";
    }
}
