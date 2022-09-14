<?php

namespace Drupal\user_provisioning\ProviderSpecific\APIHandler\UserAPIHandler;

use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\ProviderSpecific\APIHandler\moAPIHandlerInterface;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class moUserSCIMAPIHandler implements moAPIHandlerInterface
{
    private string $server_url;
    private string $bearer_token;
    private Client $http_client;
    private ImmutableConfig $config;
    private moUserProvisioningLogger $mo_logger;

    public function __construct()
    {
        $this->config = Drupal::config('user_provisioning.settings');
        $this->server_url = $this->config->get('mo_user_provisioning_scim_server_base_url');
        $this->bearer_token = $this->config->get('mo_user_provisioning_scim_server_bearer_token');
        $this->http_client = Drupal::httpClient();
        $this->mo_logger = new moUserProvisioningLogger();
    }

    /**
     * @param string $query
     * @return ResponseInterface
     * @throws Exception
     */
    public function get(string $query): ResponseInterface
    {
        $url = $this->getRequestURL($query);
        $options = ['headers' => ['Authorization' => 'Bearer ' . $this->bearer_token], 'verify' => FALSE];

        $this->mo_logger->addLog('Query url is ' . $url, __LINE__, __FUNCTION__, __FILE__);
        $this->mo_logger->addFormattedLog($options, __LINE__, __FUNCTION__, __FILE__, 'The header for resource search request is:');
        try {
            return $this->http_client->get($url, $options);
        } catch (Exception $exception) {
            // 409 conflict should be handled through the catch statement.
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param array $body
     * @return ResponseInterface
     * @throws Exception
     */
    public function post(array $body): ResponseInterface
    {

        $header = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->bearer_token,
            'Content-Type' => 'application/json',
        );

        $url = $this->postRequestURL();

        $options = [
            'headers' => $header,
            'body' => json_encode($body),
            'verify' => FALSE,
        ];

        $this->mo_logger->addLog('Query url is ' . $url, __LINE__, __FUNCTION__, __FILE__);
        $this->mo_logger->addFormattedLog($options, __LINE__, __FUNCTION__, __FILE__, 'The header and body for resource creation request is:');

        try {
            return $this->http_client->request(
                'POST',
                $url,
                $options,
            );
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function getRequestURL($query): string
    {
        return rtrim($this->server_url, '/') . '/' . $query;
    }

    /**
     * Creates and returns the URL to make POST api call for user creation.
     * @return string
     */
    private function postRequestURL(): string
    {
        return rtrim($this->server_url, '/') . '/Users';
    }

    public function put(array $body)
    {
        // TODO: Implement put() method.
    }

    public function patch(array $patch)
    {
        // TODO: Implement patch() method.
    }

    public function delete($resource_id)
    {
        // TODO: Implement delete() method.
    }
}
