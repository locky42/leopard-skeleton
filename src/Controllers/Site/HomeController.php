<?php

namespace App\Controllers\Site;

use Leopard\Core\Controllers\HtmlController;
use Leopard\Core\Attributes\Route;
use Parsedown;

/**
 * HomeController handles the logic for the home page of the site.
 * 
 * This controller extends the HtmlController from the application's core
 * and provides functionality specific to rendering the home page.
 * 
 */
class HomeController extends HtmlController
{
    public function readmy(string $path) {
        $this->view->addStyle('/assets/css/home.css');
        
        // Читаємо документацію з README.md
        $markdown = file_get_contents($path);
        
        // Конвертуємо Markdown у HTML
        $parsedown = new Parsedown();
        $documentation = str_replace(['.md', '.MD'], '', $parsedown->text($markdown));
        return $this->view->render('home', [
            'title' => $this->get('params')->get('app.name'),
            'documentation' => $documentation
        ]);
    }
    
    #[Route('/', method: 'GET')]
    public function index(): string
    {
        return $this->readmy(__DIR__ . '/../../../README.md');
    }

    #[Route('/README_UA', method: 'GET')]
    public function readmyua(): string
    {
        return $this->readmy(__DIR__ . '/../../../README_UA.md');
    }

    #[Route('/README', method: 'GET')]
    public function readmyPath(): string
    {
        return $this->readmy(__DIR__ . '/../../../README.md');
    }

    #[Route('/docs/{lang}/{view}', method: 'GET')]
    public function docs(string $lang, string $view): string
    {
        return $this->readmy(__DIR__ . '/../../../docs/' . $lang . '/' . $view . '.md');
    }
}
