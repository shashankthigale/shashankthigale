<?php

namespace Drupal\user_provisioning\Helpers;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\user_provisioning\moUserProvisioningConstants;

/**
 * Helper class for fetching all the data related to User Specific Provisioning tab
 */
class moProviderSpecificProvisioning
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
    public function providerList()
    {
        $tab_url = $this->base_url . moUserProvisioningConstants::USER_PROVISIONING . '?tab_name=provider_specific_provisioning';
        return array(
            Markup::create('<a href="' . $tab_url . '&app_name=Azure AD B2C"><img class="mo_user_provisioning_under_disabled mo_user_provisioning_img_logo" alt="Azure AD B2C" src="' . $this->url_path . '/azure.png"><br><strong>Azure AD B2C</strong>  </a>'),
            Markup::create('<a href="' . $tab_url . '&app_name=AWS Cognito"><img class="mo_user_provisioning_under_disabled mo_user_provisioning_img_logo" alt="AWS Cognito" src="' . $this->url_path . '/cognito.png"><br><strong>AWS Cognito</strong>  </a>'),
            Markup::create('<a href="' . $tab_url . '&app_name=miniorange"><img class="mo_user_provisioning_under_disabled mo_user_provisioning_img_logo" alt="miniorange" src="' . $this->url_path . '/miniorange.png" ><br><strong>Miniorange</strong>  </a>'),
            Markup::create('<a href="' . $tab_url . '&app_name=Salesforce"><img class="mo_user_provisioning_under_disabled mo_user_provisioning_img_logo" alt="Salesforce" src="' . $this->url_path . '/salesforce.png" ><br><strong>Salesforce</strong>  </a>'),
            Markup::create('<a href="' . $tab_url . '&app_name=Okta"><img class="mo_user_provisioning_under_disabled mo_user_provisioning_img_logo" alt="Okta" src="' . $this->url_path . '/okta.png" ><br><strong>Okta</strong>  </a>'),
        );

    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    public function encrypt_data($data, $key): string
    {
        $key    = openssl_digest($key, 'sha256');
        $method = 'AES-128-CBC';
        $ivSize = openssl_cipher_iv_length($method);
        $iv     = openssl_random_pseudo_bytes($ivSize);
        $strCrypt = openssl_encrypt ($data, $method, $key,OPENSSL_RAW_DATA||OPENSSL_ZERO_PADDING, $iv);
        return base64_encode($iv.$strCrypt);
    }

    /**
     * @param $data
     * @param $key
     * @param string $method
     * @return bool|string
     */
    public function decrypt_data($data, $key, string $method = "AES-128-CBC"): bool|string
    {
        $strIn  = base64_decode($data);
        $key    = openssl_digest($key, 'sha256');
        $ivSize = openssl_cipher_iv_length($method);
        $iv     = substr($strIn,0,$ivSize);
        $data   = substr($strIn,$ivSize);
        return openssl_decrypt($data, $method, $key, OPENSSL_RAW_DATA||OPENSSL_ZERO_PADDING, $iv);
    }
}
