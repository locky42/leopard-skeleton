<?php

namespace App\Controllers\Api;

use Leopard\Core\Controllers\ApiController;
use Leopard\Doctrine\EntityManager as CoreEntityManager;
use Doctrine\ORM\EntityManager;

/**
 * Class BaseApiController
 *
 * This abstract class serves as a base controller for API-related functionality.
 * It extends the ApiController class and provides shared logic or utilities
 * for other API controllers in the application.
 *
 * @package Controllers\Api
 */
abstract class BaseApiController extends ApiController
{
    /**
     * @var EntityManager The service responsible for managing entity operations.
     */
    protected EntityManager $entityManager;

    /**
     * BaseApiController constructor.
     * Initializes the controller and sets up any dependencies or configurations
     * required for API-related functionality.
     */
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = CoreEntityManager::getEntityManager();
    }
}
