<?php

namespace Drupal\user_provisioning\ProviderSpecific\Factory;

use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\ProviderSpecific\APIHandler\UserAPIHandler\moUserSCIMAPIHandler;
use Drupal\user_provisioning\ProviderSpecific\Parsers\UserParser\moUserSCIMParser;
use Drupal\user_provisioning\ProviderSpecific\ResponseProcessor\UserResponseProcessor\moUserSCIMResponseProcessor;

class moUserFactory implements moResourceFactoryInterface
{

    private string $app_name;

    public function __construct()
    {
        $app_name = \Drupal::config('user_provisioning.settings')->get('mo_user_provisioning_configured_application');
        if (empty($app_name)) {
            $app_name = moUserProvisioningConstants::DEFAULT_APP; //FIXME uncomment the above line after saving the configured application name.
        }
        $this->app_name = $app_name;
    }

    /**
     * @inheritDoc
     * */
    public function getAPIHandler()
    {
        if ($this->app_name == moUserProvisioningConstants::DEFAULT_APP) {
            return new moUserSCIMAPIHandler();
        }
    }

    /**
     * @inheritDoc
     * */
    public function getParser()
    {
        if ($this->app_name == moUserProvisioningConstants::DEFAULT_APP) {
            return new moUserSCIMParser();
        }
    }

    /**
     * @inheritDoc
     * */
    public function getResponseProcessor()
    {
        if ($this->app_name == moUserProvisioningConstants::DEFAULT_APP) {
            return new moUserSCIMResponseProcessor();
        }
    }

}
