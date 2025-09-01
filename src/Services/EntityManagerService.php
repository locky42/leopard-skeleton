<?php

namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Leopard\Core\Container;
use Leopard\Core\Services\Params;

/**
 * Service class responsible for managing entity operations.
 * Provides methods to handle entity-related logic and interactions.
 */
class EntityManagerService
{
    /**
     * @var EntityManager The entity manager instance used for database operations.
     */
    protected EntityManager $entityManager;

    /**
     * EntityManagerService constructor.
     *
     * Initializes the service and its dependencies.
     */
    public function __construct()
    {
        /** @var Container $container */
        global $container;

        if (!$container->has('params')) {
            $container->set('params', function () {
                return new Params();
            });
            
            $container->get('params')->load(__DIR__ . '/../../config/app.php');
        }
        
        $params = $container->get('params');

        $dbParams = [
            'driver'   => $params->get('database.driver'),
            'path'     => $params->get('database.database'),
            'host'     => $params->get('database.host'),
            'port'     => $params->get('database.port'),
            'user'     => $params->get('database.user'),
            'password' => $params->get('database.password'),
            'dbname'   => $params->get('database.dbname'),
        ];

        $modelsPath = $params->get('database.models_path');

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [$modelsPath],
            isDevMode: $params->get('app.isDev') ?? false
        );

        $connection = DriverManager::getConnection($dbParams, $config);
        $this->entityManager = new EntityManager($connection, $config);
    }

    /**
     * Get the entity manager instance.
     *
     * @return EntityManager The entity manager.
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
