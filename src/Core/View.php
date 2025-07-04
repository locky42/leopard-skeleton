<?php

namespace App\Core;

class View
{
    private string $viewsPath;
    private string $layout = 'layouts/main'; // Шаблон за замовчуванням
    private string $blocksPath = 'blocks';  // Шлях до блоків за замовчуванням

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function setBlocksPath(string $blocksPath): void
    {
        $this->blocksPath = $blocksPath;
    }

    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->viewsPath . '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: $templatePath");
        }

        // Extract data variables for use in the template
        extract($data);

        // Start output buffering for the content
        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        // Render the layout with the content
        $layoutPath = $this->viewsPath . '/' . $this->layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout not found: $layoutPath");
        }

        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    public function renderBlock(string $block, array $data = []): string
    {
        // Check if the block exists in the specific layout folder
        $layoutSpecificBlockPath = $this->viewsPath . '/' . $this->layout . '/' . $block . '.php';
        $defaultBlockPath = $this->viewsPath . '/' . $this->blocksPath . '/' . $block . '.php';

        $blockPath = file_exists($layoutSpecificBlockPath) ? $layoutSpecificBlockPath : $defaultBlockPath;

        if (!file_exists($blockPath)) {
            throw new \RuntimeException("Block not found: $blockPath");
        }

        extract($data);

        ob_start();
        include $blockPath;
        return ob_get_clean();
    }
}
