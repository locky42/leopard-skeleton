<?php

namespace App\Core\Helpers;

/**
 * Class System
 *
 * This class provides system-related helper methods for the application.
 * It is part of the Core module in the Leopard Skeleton project.
 *
 * @package Core\Helpers
 */
class System
{
    /**
     * Dumps the provided arguments for debugging purposes.
     *
     * This method accepts a variable number of arguments and outputs their
     * contents in a readable format. It is typically used for debugging
     * and inspecting variable values during development.
     *
     * @param mixed ...$args The arguments to be dumped.
     * @return void
     */
    public static function dump(...$args): void
    {
        foreach ($args as $arg) {
            echo '<pre>';
            var_dump($arg);
            echo '</pre>';
        }
    }

    /**
     * Dumps the given variables and terminates the script execution.
     *
     * This method is useful for debugging purposes. It accepts any number of arguments,
     * dumps their contents, and stops further execution of the script.
     *
     * @param mixed ...$args The variables to be dumped.
     * @return void
     */
    public static function dd(...$args): void
    {
        self::dump(...$args);
        die();
    }
}
