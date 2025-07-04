<?php

namespace App\Core\Controllers;

use App\Core\View;

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
