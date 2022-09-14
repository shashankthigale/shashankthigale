<?php

namespace Drupal\user_provisioning\Helpers;

use Drupal\Core\Render\Markup;
use Drupal\user_provisioning\moUserProvisioningConstants;

class moSCIMClient
{
    private $url_path;
    private $base_url;

    /**
     * Constructor for the Provider Specific Provisioning tab
     */
    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->url_path = $base_url . '/' . \Drupal::service('extension.list.module')->getPath('user_provisioning') . '/images';
    }

    /**
     * @return array of providers for provider specific provisioning
     */
    public function providerList($custom = false): array
    {
        $tab_url = $this->base_url . moUserProvisioningConstants::USER_PROVISIONING;
        if ($custom == 'custom') {
            return array(
                Markup::create('<a href="' . $tab_url . '?app_name=custom_app"><img class="mo_user_provisioning_img_logo" alt="Custom App" src="' . $this->url_path . '/customapp.png" ><br><strong>Custom App</strong>  </a>'),
            );
        } else {
            return array(
                Markup::create('<a href="' . $tab_url . '?app_name=wordpress"><img class="mo_user_provisioning_img_logo" alt="Wordpress" src="' . $this->url_path . '/wordpress.png"><br><strong>Wordpress</strong>  </a>'),
                Markup::create('<a href="' . $tab_url . '?app_name=aws_sso"><img class="mo_user_provisioning_img_logo" alt="AWS SSO" src="' . $this->url_path . '/AWS.png" ><br><strong>AWS SSO</strong>  </a>'),
                Markup::create('<a href="' . $tab_url . '?app_name=drupal"><img class="mo_user_provisioning_img_logo" alt="Drupal" src="' . $this->url_path . '/Drupal.png" ><br><strong>Drupal</strong>  </a>'),
                Markup::create('<a href="' . $tab_url . '?app_name=joomla"><img class="mo_user_provisioning_img_logo" alt="Joomla" src="' . $this->url_path . '/Joomla.png" ><br><strong>Joomla</strong>  </a>'),
            );
        }
    }
}