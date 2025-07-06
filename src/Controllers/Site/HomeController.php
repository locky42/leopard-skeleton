<?php

namespace App\Controllers\Site;

use App\Core\Attributes\Route;
use Parsedown;

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
        $this->view->addStyle('/assets/css/home.css');
        
        // Читаємо документацію з README.md
        $markdown = file_get_contents(__DIR__ . '/../../../README.md');
        
        // Конвертуємо Markdown у HTML
        $parsedown = new Parsedown();
        $documentation = $parsedown->text($markdown);

        return $this->view->render('home', [
            'title' => 'Leopard Framework',
            'documentation' => $documentation
        ]);
    }

    public function contact(): string
    {
        return "Hello from HomeController::contact";
    }
}
