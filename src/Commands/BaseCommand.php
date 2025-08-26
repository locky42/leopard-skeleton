<?php

namespace App\Commands;

use Leopard\Core\Container;
use Symfony\Component\Console\Command\Command;

/**
 * Class BaseCommand
 *
 * This abstract class serves as a base for all command classes in the application.
 * It extends the Command class and provides shared functionality or structure
 * for derived command classes.
 *
 * @package Commands
 */
abstract class BaseCommand extends Command
{
    /**
     * The dependency injection container instance.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * Constructor for the BaseCommand class.
     *
     * @param string|null $name The name of the command. Defaults to null.
     */
    public function __construct(?string $name = null)
    {
        /**
         * Calls the parent constructor with the specified command name.
         *
         * @param string|null $name The name of the command (optional).
         */
        parent::__construct($name);

        /**
         * Initializes a new instance of the Container class and assigns it to the container property.
         */
        $this->container = new Container();
        $this->container->set('params', function () {
            return new \Leopard\Core\Services\Params();
        });
        
        $this->container->get('params')->load(__DIR__ . '/../../config/app.php');
    }
}
