<?php

namespace App\Services;

class ExampleService
{
    /**
     * Example method to demonstrate service functionality.
     *
     * @param string $input An example input string.
     * @return string A processed output string.
     */
    public function processInput(string $input): string
    {
        // Example processing logic
        return strtoupper($input);
    }

    /**
     * Another example method that could interact with other services.
     *
     * @param int $number An example number.
     * @return int The square of the number.
     */
    public function calculateSquare(int $number): int
    {
        return $number * $number;
    }
}
