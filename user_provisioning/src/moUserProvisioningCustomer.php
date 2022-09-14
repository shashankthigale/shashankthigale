<?php

namespace Drupal\user_provisioning;

use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use GuzzleHttp\Exception\RequestException;

class moUserProvisioningCustomer
{
    public $email;
    public $phone;
    public $password;
    public $otp_token;
    private $default_customer_Id;
    private $default_customer_api_key;
    private ImmutableConfig $config;

    public function __construct($email, $phone = null, $password = null, $otp_token = null)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
        $this->otp_token = $otp_token;
        $this->default_customer_Id = "16555";
        $this->default_customer_api_key = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $this->config = Drupal::config('user_provisioning.settings');
    }

    /**
     * @return bool
     */
    function isCurlInstalled()
    {
        return in_array('curl', get_loaded_extensions());
    }

    /**
     * @param $add_extended_header
     * @return false[]|string[]
     */
    function getHeader($add_extended_header = FALSE)
    {

        $header = array(
            'Content-Type' => 'application/json',
            'charset' => 'UTF - 8',
            'Authorization' => 'Basic',
        );

        if ($add_extended_header) {
            /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
            $current_time_in_millis = moUserProvisioningSupport::getTimestamp();

            /* Creating the Hash using SHA-512 algorithm */
            $string_to_hash = $this->default_customer_Id . $current_time_in_millis . $this->default_customer_api_key;
            $hashValue = hash("sha512", $string_to_hash);
            $timestamp_header = number_format($current_time_in_millis, 0, '', '');
            $header = array_merge($header, array("Customer-Key" => $this->default_customer_Id, "Timestamp" => $timestamp_header, "Authorization" => $hashValue));
        }
        return $header;
    }

    /**
     * @param $url
     * @param $fields
     * @param bool $add_extended_header
     * @return false|string|void
     */
    function callService($url, $fields, $add_extended_header = false)
    {
        if (!$this->isCurlInstalled()) {
            return json_encode(array(
                "statusCode" => 'ERROR',
                "statusMessage" => 'cURL is not enabled on your site. Please enable the cURL module.',
            ));
        }
        $fieldString = is_string($fields) ? $fields : json_encode($fields);

        $header = $this->getHeader($add_extended_header);

        try {
            $response = \Drupal::httpClient()
                ->post($url, [
                    'body' => $fieldString,
                    'allow_redirects' => TRUE,
                    'http_errors' => FALSE,
                    'decode_content' => true,
                    'verify' => FALSE,
                    'headers' => $header
                ]);
            return $response->getBody()->getContents();
        } catch (RequestException $exception) {
            $error = array(
                '%apiName' => explode("moas", $url)[1],
                '%error' => $exception->getResponse()->getBody()->getContents(),
            );
            \Drupal::logger('user_provisioning')->notice('Error at %apiName of  %error', $error);
        }
    }

    /**
     * @return false|string|void
     */
    public function checkCustomer()
    {
        $url = moUserProvisioningConstants::CHECK_CUSTOMER_EXISTENCE;
        $email = $this->email;
        $fields = array(
            'email' => $email,
        );
        return $this->callService($url, $fields);
    }

    /**
     * @return false|string|void
     */
    public function getCustomerKeys()
    {
        $url = moUserProvisioningConstants::CHECK_CUSTOMER_KEY;
        $email = $this->email;
        $password = $this->password;
        $fields = array(
            'email' => $email,
            'password' => $password,
        );
        return $this->callService($url, $fields);
    }

    public function sendOtp()
    {
        $url = moUserProvisioningConstants::SEND_OTP;
        $customer_key = $this->default_customer_Id;
        $username = $this->config->get('mo_user_provisioning_customer_email');
        $fields = array(
            'customerKey' => $customer_key,
            'email' => $username,
            'authType' => 'EMAIL',
        );
        return $this->callService($url, $fields, TRUE);
    }

    public function validateOtp($transaction_id)
    {
        $url = moUserProvisioningConstants::VALIDATE_OTP;
        $fields = array(
            'txId' => $transaction_id,
            'token' => $this->otp_token,
        );
        return $this->callService($url, $fields, TRUE);

    }

    public function createCustomer()
    {
        $url = moUserProvisioningConstants::CREATE_CUSTOMER;

        $fields = array(
            'companyName' => $_SERVER['SERVER_NAME'],
            'areaOfInterest' => 'DRUPAL User Provisioning Module',
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
        );
        return $this->callService($url, $fields);
    }

}