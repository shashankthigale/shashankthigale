<?php

namespace Drupal\user_provisioning\ProviderSpecific\ResponseProcessor\UserResponseProcessor;

use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\ProviderSpecific\ResponseProcessor\moResourceResponseProcessorInterface;
use Psr\Http\Message\ResponseInterface;

class moUserSCIMResponseProcessor implements moResourceResponseProcessorInterface
{

    /**
     * @param ResponseInterface $response Response received after the API call
     * @return array Status code and the content of the response
     */
    public function get(ResponseInterface $response): array
    {
        $status_code = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        //initializing as conflict is not determined
        $conflict = moUserProvisioningConstants::SCIM_CONFLICT_UNDETERMINED;
        if ($status_code == 200) {
            $response_body = json_decode($content, true);
            if (isset($response_body['totalResults'])) {
                if ($response_body['totalResults'] == 0) {

                    //no conflict exists if no matching entity is found
                    $conflict = moUserProvisioningConstants::SCIM_NO_CONFLICT;
                } else {
                    //setting as conflict since one or more entity at the configured application is matched with the requested query
                    $content = moUserProvisioningConstants::SCIM_CONFLICT;
                }
            }
        }
        return [$status_code, $content, $conflict];
    }

    /**
     * @param ResponseInterface $response Response received after the API call
     *
     * @return array Status code and the content of the response
     */
    public function post(ResponseInterface $response): array
    {
        $status_code = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        //TODO need to add the content and its details at the database to refer for future api calls

        return [$status_code, $content];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function patch(ResponseInterface $response)
    {
        // TODO: Implement patch() method.
    }

    /**
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function put(ResponseInterface $response)
    {
        // TODO: Implement put() method.
    }

    /**
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function delete(ResponseInterface $response)
    {
        // TODO: Implement delete() method.
    }

}
