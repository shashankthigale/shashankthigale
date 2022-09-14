<?php

namespace Drupal\user_provisioning;

use Drupal;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Exception;
use Psr\Log\LoggerInterface;

class moUserProvisioningOperationsHandler
{
    /**
     * @var EntityInterface
     */
    private EntityInterface $entity;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var moUserProvisioningLogger
     */
    private moUserProvisioningLogger $mo_logger;

    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
        $this->logger = Drupal::logger('user_provisioning');
        $this->mo_logger = new moUserProvisioningLogger();
    }

    /**
     * Performs the creation of the supplied entity to the configured application
     * @throws Exception
     */
    public function insert()
    {
        try {
            $operationObject = moUserProvisioningEntityFactory::getEntityHandler($this->entity);
            $this->mo_logger->addLog("Object received, calling createResource function.", __LINE__, __FUNCTION__, __FILE__);
            return $operationObject->createResource();
        } catch (Exception $exception) {
            $this->logger->debug($exception->getMessage());
            throw $exception;
        }
    }

    public function update()
    {
        //TODO Implement update operation handler
    }

    public function delete()
    {
        //TODO Implement delete operation handler
    }
}
