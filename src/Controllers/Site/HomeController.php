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
        // Додаємо стилі та скрипти
        $this->view->addStyle('/assets/css/global.css');
        $this->view->addStyle('/assets/css/home.css');
        $this->view->addScript('/assets/js/global.js');
        $this->view->addScript('/assets/js/home.js');

        return $this->view->render('home', ['title' => 'Home Page', 'message' => 'Welcome to the Home Page!']);
    }

    public function contact(): string
    {
        return "Hello from HomeController::contact";
    }
}
