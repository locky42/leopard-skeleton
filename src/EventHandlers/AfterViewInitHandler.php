<?php

namespace App\EventHandlers;

use Leopard\Events\EventManager;
use Leopard\Core\Events\AfterViewInit;

/**
 * Event handler for AfterViewInit event.
 * 
 * This handler sets default SEO meta tags after a view is initialized.
 */
class AfterViewInitHandler implements EventHandlerInterface
{
    public function boot()
    {
        EventManager::addEvent(AfterViewInit::class, function (AfterViewInit $e) {
            $view = $e->view;
            $seo = $view->getSeo();
            $seo->setCharset('UTF-8');
            $seo->addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
        });
    }
}
