<?php

namespace Drupal\user_provisioning;

use Drupal\Core\Entity\EntityInterface;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\ProviderSpecific\EntityHandler\moUserProvisioningEntityHandlerInterface;
use Drupal\user_provisioning\ProviderSpecific\EntityHandler\moUserProvisioningUserHandler;
use http\Exception\InvalidArgumentException;

class moUserProvisioningEntityFactory
{
    /**
     * @param EntityInterface $entity
     * @return moUserProvisioningUserHandler|moUserProvisioningEntityHandlerInterface
     */
    public static function getEntityHandler(EntityInterface $entity)
    {
        if ($entity->getEntityTypeId() == 'user') {
            $moLogger = new moUserProvisioningLogger();
            $moLogger->addLog("Creating and returning moUserProvisioningUserHandler object.", __LINE__, __FUNCTION__, __FILE__);
            return new moUserProvisioningUserHandler($entity);
        } else {
            throw new InvalidArgumentException();
        }
    }
}
