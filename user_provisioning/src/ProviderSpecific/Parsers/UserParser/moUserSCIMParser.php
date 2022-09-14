<?php

namespace Drupal\user_provisioning\ProviderSpecific\Parsers\UserParser;

use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\ProviderSpecific\Parsers\moResourceParserInterface;

class moUserSCIMParser implements moResourceParserInterface
{
    private moUserProvisioningLogger $mo_logger;
    private ImmutableConfig $config;

    public function __construct()
    {
        $this->mo_logger = new moUserProvisioningLogger();
        $this->config = Drupal::config('user_provisioning.settings');
    }

    /**
     * @inheritDoc
     */
    public function search($resource_id): string
    {
        $this->mo_logger->addLog("Creating search request parameter.", __LINE__, __FUNCTION__, __FILE__);
        return 'Users?filter=userName eq "' . $resource_id . '"';
    }

    /**
     * @inheritDoc
     */
    public function get($resource_id)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function post(EntityInterface $entity)
    {
        //the bare minimum attributes
        $post_request_body = $this->getDefaultFields($entity, $this->config->get('mo_user_provisioning_app_name'));
        $this->mo_logger->addFormattedLog($post_request_body, __LINE__, __FUNCTION__, __FILE__, 'User create request body: ');

        //additional attributes
        //consider the mapping made before picking up the additional attributes

        return $post_request_body;
    }

    /**
     * @inheritDoc
     */
    public function put(array $resource)
    {
        // TODO: Implement put() method.
    }

    /**
     * @inheritDoc
     */
    public function patch(array $resource)
    {
        // TODO: Implement patch() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(array $resource)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Provides the body of the POST request for user.
     *
     * It generates an array containing the default fields of a user provided in the Drupal. It includes following:-
     *
     * -uuid
     *
     * -Username
     *
     * -Email
     *
     * -Preferred Language
     *
     * -Timezone
     *
     * -Status
     *
     *In case of the configured application is aws sso, below additional attributes are mandatory
     *
     * -first name
     *
     * -last name
     *
     *
     * @param EntityInterface $entity
     * @param string $app_name Name of the configured app
     *
     * @return array
     */
    public function getDefaultFields(EntityInterface $entity, string $app_name): array
    {
        $fields = [
            'schemas' => moUserProvisioningConstants::USER_SCHEMAS,
            'externalId' => $entity->uuid(),
            'userName' => $entity->getDisplayName(),
            "emails" => [
                [
                    "value" => $entity->getEmail(),
                    "type" => "work",
                    "primary" => TRUE,
                ],
            ],
            'displayName' => $entity->getDisplayName(),
            'preferredLanguage' => $entity->get('preferred_langcode')->value,
            'timezone' => $entity->get('timezone')->value,
            'active' => (bool)$entity->get('status')->value,
            'meta' => [
                'resourceType' => 'User',
                'created' => $entity->get('created')->value,
                'lastModified' => $entity->get('changed')->value,
            ]
        ];

        if ($app_name == moUserProvisioningConstants::AWS_SSO) {
            $givenName = $this->config->get('mo_user_provisioning_scim_client_fname_attr');
            $familyName = $this->config->get('mo_user_provisioning_scim_client_lname_attr');

            $fields['name'] = [
                'familyName' => $entity->get($familyName)->value, //TODO fetch using the mapping
                'givenName' => $entity->get($givenName)->value, //TODO fetch using the mapping
            ];

            unset($fields['schemas']);
            unset($fields['meta']);
        }
        return $fields;
    }
}
