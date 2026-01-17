<?php

namespace App\EventHandlers;

/**
 * Interface for event handlers in the application.
 * 
 * Any event handler must implement this interface to ensure
 * it has a boot method for initializing event listeners.
 */
interface EventHandlerInterface
{
    public function boot();
}
