<?php

namespace App\Core\Controllers;

use App\Core\View;

/**
 * Abstract class HtmlController
 *
 * Provides a base implementation for controllers that handle HTML rendering.
 * This class includes functionality for managing layouts, block paths, and views.
 *
 * @property string $layout The default layout file path used for rendering views.
 * @property string $blocksPath The directory path where block templates are located.
 * @property View $view The view instance used for rendering HTML content.
 */
abstract class HtmlController
{
    protected string $layout = 'layouts/main';
    protected string $blocksPath = 'blocks';
    protected View $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../src/views/' . strtolower(explode('\\', static::class)[2]));
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function setBlocksPath(string $blocksPath): void
    {
        $this->blocksPath = $blocksPath;
    }
}
