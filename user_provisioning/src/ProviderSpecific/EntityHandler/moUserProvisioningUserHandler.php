<?php

namespace Drupal\user_provisioning\ProviderSpecific\EntityHandler;

use Drupal;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user\Entity\User;
use Drupal\user_provisioning\Helpers\moUserProvisioningAudits;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\ProviderSpecific\Factory\moResourceFactoryInterface;
use Drupal\user_provisioning\ProviderSpecific\Factory\moUserFactory;
use Exception;
use Psr\Log\LoggerInterface;

class moUserProvisioningUserHandler implements moUserProvisioningEntityHandlerInterface
{

    /**
     * @var EntityInterface
     */
    private EntityInterface $entity;

    /**
     * @var moResourceFactoryInterface
     */
    private moResourceFactoryInterface $resource_factory;

    /**
     * @var moUserProvisioningAudits
     */
    private moUserProvisioningAudits $audits;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var moUserProvisioningLogger
     */
    private moUserProvisioningLogger $mo_logger;


    /**
     * @param EntityInterface $entity User or Role entity
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
        $this->resource_factory = new moUserFactory();
        $this->audits = new moUserProvisioningAudits();
        $this->logger = Drupal::logger('user_provisioning');
        $this->mo_logger = new moUserProvisioningLogger();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function searchResource(): array
    {
        try {
            [
                $status_code,
                $content,
                $conflict,
            ] = $this->resource_factory->getResponseProcessor()
                ->get($this->resource_factory
                    ->getAPIHandler()->get($this->resource_factory
                        ->getParser()->search($this->getUser()->getEmail())));

            //adding logs and audits
            $this->mo_logger->addFormattedLog(json_decode($content, TRUE), __LINE__, __FUNCTION__, 'The received user search response is:');
            $this->addUserAudit('READ', $status_code);
            $this->logger->info("body:" . $content);
            return [$status_code, $content, $conflict];
        } catch (Exception $exception) {
            $this->logger->error(__FUNCTION__ . ': ' . t($exception->getMessage()));
            $this->addUserAudit('READ');
            return [$exception->getCode(), $exception->getMessage()];
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function createResource()
    {
        try {
            [$status_code, $content, $conflict] = $this->searchResource();
            if ($conflict == moUserProvisioningConstants::SCIM_CONFLICT_UNDETERMINED) {
                $this->addUserAudit('CREATE');
                throw new Exception('Conflict. User already exists at the configured application.');
            } elseif ($conflict == moUserProvisioningConstants::SCIM_CONFLICT) {
                //TODO add the call to handle the update user call to update the user present at the configured application
                $this->mo_logger->addLog('Conflict occurred. User will be updated.', __LINE__, __FUNCTION__);
                throw new Exception('Conflict. User already exist at the configured application.');
            } else {
                //make api call to create the resource
                try {
                    [
                        $create_status_code,
                        $create_content,
                    ] = $this->resource_factory->getResponseProcessor()
                        ->post($this->resource_factory->getAPIHandler()
                            ->post($this->resource_factory->getParser()
                                ->post($this->getUser())));

                    $this->mo_logger->addFormattedLog(json_decode($create_content, true), __LINE__, __FUNCTION__, 'The received user create response is:');
                    $this->addUserAudit('CREATE', $create_status_code);

                    return $create_content;
                } catch (Exception $exception) {
                    $this->logger->error(__FUNCTION__ . ': ' . t($exception->getMessage()));
                    $this->addUserAudit('CREATE');
                    throw $exception;
                }
                // TODO process the POST response, store the received user id to db for future reference when you have to make a put or delete operation
            }
        } catch (Exception $exception) {
            $this->logger->error(__FUNCTION__ . ': ' . t($exception->getMessage()));
            $this->addUserAudit('READ');
            return null;
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function updateResource()
    {
        // TODO: Implement updateResource() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteResource()
    {
        // TODO: Implement deleteResource() method. Send the api request for deleting user, if not succeeded then add the operation in the Queue factory to be processed later.
    }


    /**
     * Returns the User object using the id of the entity of current object
     * @return User
     */
    private function getUser(): User
    {
        return User::load($this->entity->id());
    }

    /**
     * @param string $operation Operation name
     * @param int $status_code Status code of the executed operation. Default value handles the case "FAILED".
     * @return void
     */
    private function addUserAudit(string $operation, int $status_code = -1)
    {
        $this->audits->addAudit([$this->entity->id(),
            $this->entity->getAccountName(),
            time(),
            $operation,
            $status_code == -1 ? 'FAILED' : 'SUCCESS']);
    }

}
