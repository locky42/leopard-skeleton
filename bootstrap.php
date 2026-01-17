<?php

require __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Leopard\Doctrine\EntityManager as CoreEntityManager;
use Leopard\Core\Container;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Console\Application;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

global $container;
$container = new Container();

$container->set('params', function () {
    return new \Leopard\Core\Services\Params();
});

$container->get('params')->load(__DIR__ . '/config/app.php');

$params = $container->get('params');

$application = new Application($params->get('app.name'), $params->get('app.version'));

/** Add Doctrine commands */
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/src/Models'],
    isDevMode: true
);

$connection = DriverManager::getConnection([
    'driver' => $params->get('database.driver'),
    'path' => $params->get('database.database'),
    'host' => $params->get('database.host'),
    'port' => $params->get('database.port'),
    'user' => $params->get('database.user'),
    'password' => $params->get('database.password'),
    'dbname' => $params->get('database.dbname'),
]);

$entityManager = new EntityManager($connection, $config);
$entityManagerProvider = new SingleManagerProvider($entityManager);

CoreEntityManager::setEntityManager($entityManager);

foreach (glob(__DIR__ . '/src/EventHandlers/*.php') as $filename) {
    $className = 'App\EventHandlers\\' . basename($filename, '.php');
    if (class_exists($className) && is_subclass_of($className, \App\EventHandlers\EventHandlerInterface::class)) {
        $handler = new $className();
        $handler->boot();
    }
}
