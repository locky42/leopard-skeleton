<?php

namespace App\Core;

/**
 * The View class is responsible for handling the rendering of templates
 * and managing the presentation layer of the application.
 * 
 * It provides methods to load and render views, passing data to templates
 * and ensuring separation of concerns between the application logic and
 * the user interface.
 */
class View
{
    private string $viewsPath;
    private string $layout = 'layouts/main'; // Шаблон за замовчуванням
    private string $blocksPath = 'blocks';  // Шлях до блоків за замовчуванням

    /**
     * Constructor for the View class.
     *
     * @param string $viewsPath The path to the directory containing view files.
     */
    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
    }

    /**
     * Sets the layout for the view.
     *
     * @param string $layout The name of the layout to be used.
     * @return void
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Sets the path to the blocks directory.
     *
     * @param string $blocksPath The file path to the blocks directory.
     * @return void
     */
    public function setBlocksPath(string $blocksPath): void
    {
        $this->blocksPath = $blocksPath;
    }

    /**
     * Renders a template with the provided data and returns the resulting output as a string.
     *
     * @param string $template The path or identifier of the template to be rendered.
     * @param array $data An associative array of data to be passed to the template. Default is an empty array.
     * @return string The rendered output of the template.
     */
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

    /**
     * Renders a specific block with the provided data.
     *
     * @param string $block The name of the block to render.
     * @param array $data An associative array of data to pass to the block. Defaults to an empty array.
     * @return string The rendered content of the block.
     */
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
