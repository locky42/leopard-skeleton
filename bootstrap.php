<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/contract-mappings.php';

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Application;
use Leopard\Core\Container;
use Leopard\Events\EventManager as LeopardEventManager;
use Leopard\Doctrine\EntityManager as CoreEntityManager;
use Leopard\Doctrine\Events\AfterInitEventManagerEvent;
use Leopard\Doctrine\Events\BeforeInitEventManagerEvent;
use Leopard\Doctrine\Events\BeforeInitEntityManagerEvent;

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
    paths: [
        __DIR__ . '/src/Models',
        __DIR__ . '/src/Models/Admin',
    ],
    isDevMode: true
);

LeopardEventManager::doEvent(BeforeInitEventManagerEvent::class);

$eventManager = new EventManager();

LeopardEventManager::doEvent(AfterInitEventManagerEvent::class, $eventManager);

$connection = DriverManager::getConnection([
    'driver' => $params->get('database.driver'),
    'path' => $params->get('database.database'),
    'host' => $params->get('database.host'),
    'port' => $params->get('database.port'),
    'user' => $params->get('database.user'),
    'password' => $params->get('database.password'),
    'dbname' => $params->get('database.dbname'),
], null, $eventManager);

LeopardEventManager::doEvent(BeforeInitEntityManagerEvent::class);

$entityManager = new EntityManager($connection, $config, $eventManager);
$entityManagerProvider = new SingleManagerProvider($entityManager);

CoreEntityManager::setEntityManager($entityManager);

foreach (glob(__DIR__ . '/src/EventHandlers/*.php') as $filename) {
    $className = 'App\EventHandlers\\' . basename($filename, '.php');
    if (class_exists($className) && is_subclass_of($className, \App\EventHandlers\EventHandlerInterface::class)) {
        $handler = new $className();
        $handler->boot();
    }
}