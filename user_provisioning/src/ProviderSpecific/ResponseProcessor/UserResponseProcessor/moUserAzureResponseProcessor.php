<?php

namespace Drupal\user_provisioning\ProviderSpecific\ResponseProcessor\UserResponseProcessor;

use Drupal\Component\Serialization\Json;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\ProviderSpecific\ResponseProcessor\moResourceResponseProcessorInterface;
use Psr\Http\Message\ResponseInterface;

class moUserAzureResponseProcessor implements moResourceResponseProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function get(ResponseInterface $response)
    {
        $status_code = $response->getStatusCode();
        $content = $response->getBody()->getContents();
        $content = Json::decode($content);
        $user = $content['value'];

        //initializing as conflict is not determined
        $conflict = moUserProvisioningConstants::AZURE_CONFLICT_UNDETERMINED;
        if ($status_code == 200){
            if (empty($user)) {
                //no conflict exists if no matching entity is found
                $conflict = moUserProvisioningConstants::AZURE_NO_CONFLICT;
            }
            else{
                //setting as conflict since one or more entity at the configured application is matched with the requested query
                $conflict = moUserProvisioningConstants::AZURE_CONFLICT;
            }
        }
        return [$status_code, $content, $conflict];
    }

    /**
     * @inheritDoc
     */
    public function post(ResponseInterface $response)
    {
        $status_code = $response->getStatusCode();
        $content = $response->getBody()->getContents();
        return [$status_code, $content];
    }

    /**
     * @inheritDoc
     */
    public function patch(ResponseInterface $response)
    {
        // TODO: Implement patch() method.
    }

    /**
     * @inheritDoc
     */
    public function put(ResponseInterface $response)
    {
        // TODO: Implement put() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(ResponseInterface $response)
    {
        // TODO: Implement delete() method.
    }
}