<?php

namespace App\Controllers\Site;

use App\Core\Attributes\Route;

/**
 * HomeController handles the logic for the home page of the site.
 * 
 * This controller extends the HtmlController from the application's core
 * and provides functionality specific to rendering the home page.
 * 
 */
class HomeController extends \App\Core\Controllers\HtmlController
{
    #[Route('/', method: 'GET')]
    public function index(): string
    {
        $this->view->addStyle('/assets/css/header.css');
        
        return $this->view->render('home', ['title' => 'Leopard Framework', 'message' => 'Welcome to the Home Page!']);
    }

    public function contact(): string
    {
        return "Hello from HomeController::contact";
    }
}
