<?php

namespace Drupal\user_provisioning;

use Drupal;
use Drupal\Core\Config\ImmutableConfig;

class moUserProvisioningSupport
{

    private ImmutableConfig $config;
    public $email;
    public $phone;
    public $query;
    public $query_type;

    public function __construct($email, $phone, $query, $query_type)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->query = $query;
        $this->query_type = $query_type;
        $this->config = Drupal::config('user_provisioning.settings');
    }

    /**
     * This function is written for sending the Support query
     * @return bool
     */
    public function sendSupportQuery()
    {
        $modules_info = \Drupal::service('extension.list.module')->getExtensionInfo('user_provisioning');
        $modules_version = $modules_info['version'];

        if ($this->query_type == 'Trial Request' || $this->query_type == 'Call Request') {

            $url = moUserProvisioningConstants::MO_NOTIFY_SEND;

            // Code commented for Call support request. Please do not delete
            // $request_for = $this->query_type == 'Trial Request' ? 'Trial' : 'Setup Meeting/Call';
            $request_for = 'Trial';

            $subject = $request_for . ' request for Drupal-' . \DRUPAL::VERSION . ' User Provisioning Module | ' . $modules_version;
            $this->query = $request_for . ' requested for - ' . $this->query;

            $customerKey = $this->config->get('miniorange_oauth_client_customer_id');
            $apikey = $this->config->get('miniorange_oauth_client_customer_api_key');

            if ($customerKey == '') {
                $customerKey = "16555";
                $apikey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
            }

            $currentTimeInMillis = self::getTimestamp();
            $stringToHash = $customerKey . $currentTimeInMillis . $apikey;
            $hashValue = hash("sha512", $stringToHash);

            // Code written for Call support request. Please do not delete
            /*            if ($this->query_type == 'Call Request'){
                            $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number:' . $this->phone . '<br><br>Email:<a href="mailto:' . $this->email . '" target="_blank">' . $this->email . '</a><br><br> Timezone: <b>'. $this->mo_timezone .'</b><br><br> Date: <b>'. $this->mo_date .'</b>&nbsp;&nbsp; Time: <b>'. $this->mo_time .'</b><br><br>Query:[DRUPAL ' . Utilities::mo_get_drupal_core_version() . ' OAuth Login Free | PHP '. phpversion() .' | '. $modules_version . ' ] ' . $this->query . '</div>';
                        }else {
                            $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number:' . $this->phone . '<br><br>Email:<a href="mailto:' . $this->email . '" target="_blank">' . $this->email . '</a><br><br>Query:[DRUPAL ' . Utilities::mo_get_drupal_core_version() . ' OAuth Login Free | PHP '. phpversion() .' | ' . $modules_version . ' ] ' . $this->query . '</div>';
                        }*/

            $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number:' . $this->phone . '<br><br>Email:<a href="mailto:' . $this->email . '" target="_blank">' . $this->email . '</a><br><br>Query:[DRUPAL ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' User Provisioning Free | PHP ' . phpversion() . ' | ' . $modules_version . ' ] ' . $this->query . '</div>';

            $fields = array(
                'customerKey' => $customerKey,
                'sendEmail' => true,
                'email' => array(
                    'customerKey' => $customerKey,
                    'fromEmail' => $this->email,
                    'fromName' => 'miniOrange',
                    'toEmail' => moUserProvisioningConstants::SUPPORT_EMAIL,
                    'toName' => moUserProvisioningConstants::SUPPORT_EMAIL,
                    'subject' => $subject,
                    'content' => $content
                ),
            );

            $header = array('Content-Type' => 'application/json',
                'Customer-Key' => $customerKey,
                'Timestamp' => $currentTimeInMillis,
                'Authorization' => $hashValue);

        } else {

            $this->query = '[Drupal ' . \DRUPAL::VERSION . ' User Provisioning Module | PHP ' . phpversion() . ' | ' . $modules_version . '] ' . $this->query;
            $fields = array(
                'company' => $_SERVER['SERVER_NAME'],
                'email' => $this->email,
                'phone' => $this->phone,
                'ccEmail' => moUserProvisioningConstants::SUPPORT_EMAIL,
                'query' => $this->query,
            );

            $url = moUserProvisioningConstants::CONTACT_US;

            $header = array('Content-Type' => 'application/json',
                'charset' => 'UTF-8',
                'Authorization' => 'Basic'
            );
        }

        $field_string = json_encode($fields);
        $mo_user_provisioning_customer = new moUserProvisioningCustomer(null, null, null, null);
        $response = $mo_user_provisioning_customer->callService($url, $field_string, $header);

        return TRUE;
    }

    /**
     * This function is written to get the timestamp
     * @return string
     */
    public static function getTimestamp()
    {
        $url = moUserProvisioningConstants::GET_TIMESTAMP;
        $mo_user_provisioning_customer = new moUserProvisioningCustomer(null, null, null, null);
        $content = $mo_user_provisioning_customer->callService($url, [], []);

        if (empty($content)) {
            $currentTimeInMillis = round(microtime(true) * 1000);
            $currentTimeInMillis = number_format($currentTimeInMillis, 0, '', '');
        }
        return empty($content) ? $currentTimeInMillis : $content;
    }
}