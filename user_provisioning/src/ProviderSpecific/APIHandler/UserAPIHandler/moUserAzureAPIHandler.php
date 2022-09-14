<?php

namespace Drupal\user_provisioning\ProviderSpecific\APIHandler\UserAPIHandler;

use Drupal\azure_ad\moAzureConstants;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\ProviderSpecific\APIHandler\moAPIHandlerInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class moUserAzureAPIHandler implements moAPIHandlerInterface
{
    public string $token_endpoint;
    public string $userinfo_endpoint;
    public string $scope;
    private string $client_id;
    private string $client_secret;
    private string $tenant;
    private string $upn_id;
    private $access_token;
    private Client $http_client;
    private ImmutableConfig $config;
    private moUserProvisioningLogger $mo_logger;

    public function __construct()
    {
        $this->config = \Drupal::config('azure_ad.settings');
        $this->token_endpoint = moAzureConstants::TOKEN_ENDPOINT;
        $this->userinfo_endpoint = moAzureConstants::USERINFO_ENDPOINT;
        $this->scope = moAzureConstants::SCOPE;
        $this->client_id = $this->config->get('mo_azure_application_id');
        $this->client_secret = $this->config->get('mo_azure_application_secret');
        $this->tenant = $this->config->get('mo_azure_tenant_id');
        $this->upn_id = $this->config->get('mo_azure_test_upn');
        $this->http_client = \Drupal::httpClient();
        $this->access_token = $this->moAzureGetAccessToken();
        $this->mo_logger = new moUserProvisioningLogger();
    }

    public function moAzureGetUserDetails(){
        $user_info_url = $this->userinfo_endpoint.urlencode($this->upn_id);
        $resourceOwner = $this->moAzureGetResourceOwner($user_info_url, $this->access_token);
        $resourceOwner['manager'] = $this->moAzureGetResourceOwner($user_info_url.'/manager', $this->access_token, true);
        return $resourceOwner;
    }

    private function moAzureGetAccessToken(){

        $token_endpoint = str_replace( '{tenant}', $this->tenant , $this->token_endpoint);
        $response = $this->callService($token_endpoint,
            'grant_type=client_credentials&client_id=' . urlencode($this->client_id) . '&client_secret=' . urlencode($this->client_secret) . '&scope=' . $this->scope,
            array('Content-Type' => 'application/x-www-form-urlencoded')
        );

        $content = Json::decode($response,true);

        if (isset($content["error"]) || isset($content["error_description"])) {
            if (isset($content["error"]) && is_array($content["error"])) {
                $content["error"] = $content["error"]["message"];
            }
        } else if (isset($content["access_token"])) {
            $access_token = $content["access_token"];
        } else {
            exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
        }

        return $access_token;
    }

    public function moAzureGetProfilePic($upn){
        $profile_pic_url = $this->userinfo_endpoint.urlencode($upn).'/photo/$value';
        return $this->moAzureGetMedia($profile_pic_url, $this->access_token);
    }

    private function moAzureGetMedia($url, $access_token){
        $response = $this->callService($url,
            NULL,
            array('Content-Type' => 'image/jpg', 'Authorization' => 'Bearer ' . $access_token),
            'GET', true
        );

        if (is_null($response)){
            return null;
        }else{
            return base64_encode($response->getContents());
        }
    }

    private function callService($url, $fields, $header = FALSE, $get_post = '', $is_manager = false){
        if (!$this->isCurlInstalled()) {
            return Json::encode(array(
                "statusCode" => 'ERROR',
                "statusMessage" => 'cURL is not enabled on your site. Please enable the cURL module.',
            ));
        }
        $fieldString = is_string($fields) ? $fields : Json::encode($fields);

        if ($get_post == 'GET'){
            try{
                $response = $this->http_client
                    ->get($url, [
                        'headers' => $header,
                        'verify' => FALSE,
                    ]);
                return $response->getBody();
            }
            catch (Exception $exception)
            {
                $error = $exception->getResponse()->getBody()->getContents();
                \Drupal::logger('azure_ad')->notice('Error: <pre><code>'. print_r($error, true) . '</code></pre>');
                if (!$is_manager)
                    return $error;
            }
        } else {
            try {
                $response = $this->http_client
                    ->post($url, [
                        'body' => $fieldString,
                        'allow_redirects' => TRUE,
                        'http_errors' => FALSE,
                        'decode_content' => TRUE,
                        'verify' => FALSE,
                        'headers' => $header
                    ]);
                return $response->getBody()->getContents();
            } catch (RequestException $exception) {

                $error = array(
                    '%error' => $exception->getResponse()->getBody()->getContents(),
                );
                \Drupal::logger('azure_ad')->notice('Error:  %error', $error);
                $this->show_error_message($error);

            }
        }
        return null;
    }

    private function isCurlInstalled(): bool
    {
       return in_array('curl', get_loaded_extensions());
    }

    private function moAzureGetResourceOwner($resource_owner_details_url, $access_token, $is_manager = false){
        $response = $this->callService($resource_owner_details_url,
            NULL,
            array('Authorization' => 'Bearer ' . $access_token),
            'GET', $is_manager
        );

        return $this->CheckResponseIfAnyErrors($response);
    }

    public function CheckResponseIfAnyErrors($response)
    {
        if (isset($response) && !empty($response)) {
            return Json::decode($response);
        }
        return null;
    }

    public static function show_error_message($get)
    {
        echo '<div style="font-family:Calibri;padding:0 3%;">
            <div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
            ERROR
            </div><div style="color: #a94442;font-size:14pt; margin-bottom:20px;">';

        foreach ($get as $key => $val) {
            if ($key == 'state') {
                continue;
            }
            echo '<p><strong>' . $key . ': </strong>' . $val . '</p>';
        }
        echo '</div></div>';
        exit;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get($resource_id)
    {
        $url = str_replace('resource_id', $resource_id,moAzureConstants::CHECK_USER);
        $this->mo_logger->addLog('Query url is ' . $url, __LINE__, __FUNCTION__, __FILE__);

        $options = ['headers' =>
            ['Authorization' => 'Bearer ' . $this->access_token, 'Content-Type' => 'application/json',]
        ];

        $this->mo_logger->addLog('Query url is ' . $url, __LINE__, __FUNCTION__, __FILE__);
        $this->mo_logger->addFormattedLog($options, __LINE__, __FUNCTION__, __FILE__, 'The header for resource search request is:');

        try {
            return $this->http_client->get($url, $options);
        }
        catch (Exception $exception){
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function post(array $body)
    {
        $header = [
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type' => 'application/json',
        ];

        $options = [
            'headers' => $header,
            'body' => Json::encode($body),
        ];

        $this->mo_logger->addLog('Query url is ' . $this->userinfo_endpoint, __LINE__, __FUNCTION__, __FILE__);
        $this->mo_logger->addFormattedLog($options, __LINE__, __FUNCTION__, __FILE__, 'The header and body for resource creation request is:');

        try {
            return $this->http_client->request(
                'POST',
                $this->userinfo_endpoint,
                $options,
            );
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function put(array $body)
    {
        // TODO: Implement put() method.
    }

    /**
     * @inheritDoc
     */
    public function patch(array $patch)
    {
        // TODO: Implement patch() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($resource_id)
    {
        // TODO: Implement delete() method.
    }
}