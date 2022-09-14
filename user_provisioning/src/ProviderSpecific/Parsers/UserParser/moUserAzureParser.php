<?php

namespace Drupal\user_provisioning\ProviderSpecific\Parsers\UserParser;

use Drupal;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user_provisioning\Helpers\moProviderSpecificProvisioning;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\ProviderSpecific\Parsers\moResourceParserInterface;

class moUserAzureParser implements moResourceParserInterface
{

    private moUserProvisioningLogger $mo_logger;
    private ImmutableConfig $config;
    private Config $config_factory;

    public function __construct()
    {
        $this->mo_logger = new moUserProvisioningLogger();
        $this->config = \Drupal::config('azure_ad.settings');
        $this->config_factory = Drupal::configFactory()->getEditable('azure_ad.settings');
    }
    /**
     * @inheritDoc
     */
    public function search($resource_id): string
    {
        $this->mo_logger->addLog("Creating search request parameter.", __LINE__, __FUNCTION__, __FILE__);
        return $resource_id;
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
        $username = $entity->getDisplayName();

        $user_info = $this->config->get('mo_azure_ad_'.$username.'_dependency');
        $this->config_factory->clear('mo_azure_ad_'.$username.'_dependency')->save();
        $prov_specific_provisioning = new moProviderSpecificProvisioning();

        $azure_password = isset($user_info) && !empty($user_info) ? $prov_specific_provisioning->decrypt_data($user_info, $username) : 'xWwvJ]6NMw+bWH-d';

        $userObject = '{
             "accountEnabled":true,
             "passwordProfile" : {
             "password": "'. $azure_password.'",
             "forceChangePasswordNextSignIn": true
             },
             "mailNickname": "'.$username.'",
            "passwordPolicies": "DisablePasswordExpiration"
        }';

        $userObject = Json::decode($userObject);
        $userObject['userPrincipalName'] = $username.'@'.$this->config->get('mo_azure_tenant_name');
        $userObject['displayName'] = $username;
        return $userObject;
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
}