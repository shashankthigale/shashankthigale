<?php

namespace Drupal\oauth_login_oauth2;
use Drupal\oauth_login_oauth2\Controller\miniorange_oauth_clientController;
use Drupal\oauth_login_oauth2\Utilities;
/**
 * @file
 * This class represents support information for customer.
 */
/**
 * @file
 * Contains miniOrange Support class.
 */
class MiniorangeOAuthClientSupport {
  public $email;
  public $phone;
  public $query;
  public $query_type;
  public $mo_timezone;
  public $mo_date;
  public $mo_time;

  public function __construct($email, $phone, $query, $query_type = '', $mo_timezone = '', $mo_date = '', $mo_time = '') {
    $this->email = $email;
    $this->phone = $phone;
    $this->query = $query;
    $this->query_type = $query_type;
    $this->mo_timezone = $mo_timezone;
    $this->mo_date = $mo_date;
    $this->mo_time = $mo_time;
  }

  /**
	 * Send support query.
	 */
    public function sendSupportQuery()
    {

      $modules_info = \Drupal::service('extension.list.module')->getExtensionInfo('oauth_login_oauth2');
      $modules_version = $modules_info['version'];

      if ($this->query_type == 'Trial Request' || $this->query_type == 'Call Request' ) {

        $url = MiniorangeOAuthClientConstants::BASE_URL . '/moas/api/notify/send';
        $request_for = $this->query_type == 'Trial Request' ? 'Trial' : 'Setup Meeting/Call';

        $subject = $request_for.' request for Drupal-' . \DRUPAL::VERSION . ' OAuth Login Module | ' .$modules_version;
        $this->query = $request_for.' requested for - ' . $this->query;

        $customerKey = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_id');
        $apikey = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_api_key');
        if ($customerKey == '') {
          $customerKey = "16555";
          $apikey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        }

        $currentTimeInMillis = Utilities::get_oauth_timestamp();
        $stringToHash = $customerKey . $currentTimeInMillis . $apikey;
        $hashValue = hash("sha512", $stringToHash);

        if ($this->query_type == 'Call Request'){
          $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number:' . $this->phone . '<br><br>Email:<a href="mailto:' . $this->email . '" target="_blank">' . $this->email . '</a><br><br> Timezone: <b>'. $this->mo_timezone .'</b><br><br> Date: <b>'. $this->mo_date .'</b>&nbsp;&nbsp; Time: <b>'. $this->mo_time .'</b><br><br>Query:[DRUPAL ' . Utilities::mo_get_drupal_core_version() . ' OAuth Login Free | PHP '. phpversion() .' | '. $modules_version . ' ] ' . $this->query . '</div>';
        }else {
          $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number:' . $this->phone . '<br><br>Email:<a href="mailto:' . $this->email . '" target="_blank">' . $this->email . '</a><br><br>Query:[DRUPAL ' . Utilities::mo_get_drupal_core_version() . ' OAuth Login Free | PHP '. phpversion() .' | ' . $modules_version . ' ] ' . $this->query . '</div>';
        }

        $fields = array(
          'customerKey' => $customerKey,
          'sendEmail' => true,
          'email' => array(
            'customerKey' => $customerKey,
            'fromEmail' => $this->email,
            'fromName' => 'miniOrange',
            'toEmail' => MiniorangeOAuthClientConstants::SUPPORT_EMAIL,
            'toName' => MiniorangeOAuthClientConstants::SUPPORT_EMAIL,
            'subject' => $subject,
            'content' => $content
          ),
        );

        $header = array('Content-Type'=> 'application/json',
          'Customer-Key' => $customerKey,
          'Timestamp' => $currentTimeInMillis,
          'Authorization' => $hashValue);

      } else {

        $this->query = '[Drupal ' . \DRUPAL::VERSION . ' OAuth Login Module | PHP '. phpversion() .' | ' . $modules_version.'] ' . $this->query;
        $fields = array(
          'company' => $_SERVER['SERVER_NAME'],
          'email' => $this->email,
          'phone' => $this->phone,
          'ccEmail' => MiniorangeOAuthClientConstants::SUPPORT_EMAIL,
          'query' => $this->query,
        );

        $url = MiniorangeOAuthClientConstants::BASE_URL . '/moas/rest/customer/contact-us';

        $header = array('Content-Type'=> 'application/json',
          'charset' => 'UTF-8',
          'Authorization' => 'Basic');
      }

      $field_string = json_encode($fields);
      $response = Utilities::callService($url, $field_string, $header);

      return TRUE;

    }
}
