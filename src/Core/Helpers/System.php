<?php

namespace App\Core\Helpers;

class System
{
    public static function dump(...$args): void
    {
        foreach ($args as $arg) {
            echo '<pre>';
            var_dump($arg);
            echo '</pre>';
        }
    }

    public static function dd(...$args): void
    {
        self::dump(...$args);
        die();
    }
}
