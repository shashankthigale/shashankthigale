<?php
 /**
 * @file
 * Contains \Drupal\miniorange_oauth_client\Controller\DefaultController.
 */
namespace Drupal\oauth_login_oauth2\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\oauth_login_oauth2\Form\MiniorangeConfigOAuthClient;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Extension;
use Drupal\Component\Utility\Html;
use Drupal\oauth_login_oauth2\handler;
use Drupal\oauth_login_oauth2\AuthorizationEndpoint;
use Drupal\oauth_login_oauth2\AccessToken;
use Drupal\oauth_login_oauth2\UserResource;
use Drupal\oauth_login_oauth2\Utilities;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\formBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;


class miniorange_oauth_clientController extends ControllerBase {

    protected $formBuilder;
    public function __construct(FormBuilder $formBuilder) {
      $this->formBuilder = $formBuilder;
    }

    public static function create(ContainerInterface $container) {
      return new static(
        $container->get("form_builder")
      );
    }

    //handles the feedback flow of the module
    public function miniorange_oauth_client_feedback_func(){
        global $base_url;
        handler::sendFeedbackEmail();
        /**
         * Uninstalling the OAuth login module after sending the feedback email
         */
        \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_uninstall_status',1)->save();
        \Drupal::service('module_installer')->uninstall(['oauth_login_oauth2']);
        if(!empty(\Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url')))
            $baseUrlValue = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url');
        else
            $baseUrlValue = $base_url;
        $uninstall_redirect = $baseUrlValue.'/admin/modules';
        $response = new RedirectResponse($uninstall_redirect);
        $response->send();
        return new Response();
    }

    public function miniorange_oauth_client_mo_login()
    {
      $code = isset($_GET['code']) ? Html::escape($_GET['code']) : '';
      $state = isset($_GET['state']) ? Html::escape($_GET['state']) : '';

      if (!isset($code)) {
        Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Code is not set in the URL. Get parameters: '. $_GET);

        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '
            <div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
            ERROR
            </div>
            <div style="color: #a94442;font-size:14pt; margin-bottom:20px;">';

        foreach ($_GET as $key => $val) {
          if ($key == 'state') {
            continue;
          }
          echo '<p><strong>' . $key . ': </strong>' . $val . '</p>';
        }
        echo '</div>
            </div>';
        exit;
      } else {
        $currentappname = "";
        if (isset($state) && !empty($state)) {
          $currentappname = base64_decode($state);
        }
        if (empty($currentappname)) {
          exit('No request found for this application.');
        }
      }

      if (isset($code) && isset($state)) {
        if (session_id() == '' || !isset($_SESSION)) {
          session_start();
        }
      }
      // Getting Access Token

      $config = \Drupal::config('oauth_login_oauth2.settings');
      $email_attr = $config->get('miniorange_oauth_client_email_attr_val');

      $callback_url = $config->get('miniorange_auth_client_callback_uri');
      $client_id = $config->get('miniorange_auth_client_client_id');
      $client_secret = $config->get('miniorange_auth_client_client_secret');
      $access_token_endpoint = $config->get('miniorange_auth_client_access_token_ep');
      $userinfo_endpoint = $config->get('miniorange_auth_client_user_info_ep');
      $parse_from_header = $config->get('miniorange_oauth_send_with_header_oauth');
      $parse_from_body = $config->get('miniorange_oauth_send_with_body_oauth');

      $accessToken = AccessToken::getAccessToken($access_token_endpoint, 'authorization_code', $client_id, $client_secret, $code, $callback_url, $parse_from_header, $parse_from_body);
      Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Access Token received: '. $accessToken);

      if (!$accessToken) {
        print_r('Invalid token received.');
        exit;
      }

      if (str_ends_with($userinfo_endpoint, "=")) {
        $userinfo_endpoint .= $accessToken;
      }
      $resourceOwner = UserResource::getResourceOwner($userinfo_endpoint, $accessToken);

      /*
     *   Test Configuration
     */
      if (isset($_COOKIE['Drupal_visitor_mo_oauth_test']) && ($_COOKIE['Drupal_visitor_mo_oauth_test'] == true)) {
        $_COOKIE['Drupal_visitor_mo_oauth_test'] = 0;
        $module_path = \Drupal::service('extension.list.module')->getPath('oauth_login_oauth2');
        $username = isset($resourceOwner['email']) ? $resourceOwner['email'] : 'User';
        $someattrs = '';
        Utilities::show_attr($resourceOwner, $someattrs, 0, '', '<tr style="text-align:center;">', "<td style='font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;'>");
        $resourceOwner_encoded = json_encode($resourceOwner);
        \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_client_attr_list_from_server', $resourceOwner_encoded)->save();
        \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_client_show_attr_list_from_server', $resourceOwner_encoded)->save();
        echo '<div style="font-family:Calibri;padding:0 3%;">';

        echo '<div style="display:block;text-align:center;margin-bottom:4%;">
                        <img style="width:15%;"src="' . $module_path . '/includes/images/green_check.png">
                      </div>';

        echo '<span style="font-size:13pt;"><b>Hello</b>, ' . $username . '</span><br><br><div style="background-color:#dff0d8;padding:1%;">Your Test Connection is successful. Now, follow the below steps to complete the last step of your configuration:</div><span style="font-size:13pt;"><br><b></b>Please select the <b>Attribute Name</b> in which you are getting <b>Email ID.</b><br><br></span><div style="background-color: #dddddd; margin-left: 2%; margin-right: 3%">';

        self::miniorange_oauth_client_update_email_username_attribute($resourceOwner);

        echo '<br>&emsp;<i style="font-size: small">You can also map the Username attribute from the Attribute and Role Mapping tab in the module.</i><br><br></div>
                    <br><i>Click on the <b>Done</b> button to save your changes.</i><br>';

        echo '<div style="margin:3%;display:block;text-align:center;"><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;
                            border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;
                            box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="button" value="Done" onClick="save_and_done();"></div>
                    <script>
                        function save_and_done(){
                          var email_attr = document.getElementById("mo_oauth_email_attribute").value;
                          var index = window.location.href.indexOf("?");
                          var url = window.location.href.slice(0,index).replace("mo_login","mo_post_testconfig/?field_selected="+email_attr);
                          window.opener.location.href= url;
                          self.close();
                        }
                    </script>';


        echo '<p><b> ATTRIBUTES RECEIVED:</b></p><table style="border-collapse:collapse;border-spacing:0; display:table;width:100%; font-size:13pt;background-color:#EDEDED;">
                          <tr style="text-align:center;">
                              <td style="font-weight:bold;border:2px solid #949090;padding:2%;width: fit-content;">ATTRIBUTE NAME</td>
                              <td style="font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td>
                          </tr>';
        echo $someattrs;
        echo '</table></div>';

        return new Response();
        exit();
      }

      if (!empty($email_attr))
        $email = self::getnestedattribute($resourceOwner, $email_attr);          //$resourceOwner[$email_attr];

      global $base_url;

      Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Email Attribute: '. $email);

      /*************==============Attributes not mapped check===============************/
      if (empty($email)) {
        Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Email is empty.');

        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>Email address does not received.</p>
                                    <p>Check your <b>Attribute Mapping</b> configuration.</p>
                                    <p><strong>Possible Cause: </strong>Email Attribute field is not configured.</p>
                                </div>
                                <div style="margin:3%;display:block;text-align:center;"></div>
                                <div style="margin:3%;display:block;text-align:center;">
                                    <form action="' . $base_url . '" method ="post">
                                        <input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="submit" value="Done">
                                    </form>
                                </div>';
        exit;
        return new Response();
      }
      //Validates the email format
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format of the received value";
        exit;
      }

      $account = '';
      if (!empty($email))
        $account = user_load_by_mail($email);

      global $user;
      /**
       * Creating a new user in case the user does not exists in the Drupal database
       */
      if (!isset($account->uid)) {
        Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'User does not exists.');

        echo '<div style="font-family:Calibri;padding:0 3%;">';
        echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div><div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>User Not Found in Drupal.</p><p>You can only log in the existing Drupal users in this version of the module.<br><br>Please upgrade to either the <a href="https://plugins.miniorange.com/drupal-oauth-client#pricing" target="_blank">Standard, Premium or the Enterprise </a> version of the module in order to create unlimited new users.</p></div><div style="margin:3%;display:block;text-align:center;"></div><div style="margin:3%;display:block;text-align:center;"><form action="'.$base_url.'" method ="post"><input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="submit" value="Done"></form></div>';
        exit;
        return new Response();
      }
      $user = User::load($account->id());
      Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'SSO user ID: '.$account->id());

      user_login_finalize($account);
      $redirectURL = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : $base_url;
      $response = new RedirectResponse($redirectURL);
      $response->send();
      return new Response();
    }

    /**
     * This function is used to get some specific values from the resource
     */
    function getnestedattribute($resource, $key){
        if(empty($key))
            return "";
        $keys = explode(".",$key);
        $currentkey = "";
        if(sizeof($keys)>1){
            $currentkey = $keys[0];
            if(isset($resource[$currentkey]))
                return self::getnestedattribute($resource[$currentkey], str_replace($currentkey.".","",$key));
        }
        else{
            $currentkey = $keys[0];
            if(isset($resource[$currentkey]))
            {
                if(is_array($resource[$currentkey]))
                {
                    $resource = $resource[$currentkey];
                    return $resource[0];
                }
                else{
                    return $resource[$currentkey];
                }
            }
        }
    }
    /**
     * Handling Test Configuration Flow
     */
    public function test_mo_config(){
         user_cookie_save(array("mo_oauth_test" => true));
         AuthorizationEndpoint::mo_oauth_client_initiateLogin();
         return new Response();
    }

    public function openDemoRequestForm() {
      $response = new AjaxResponse();
      $modal_form = $this->formBuilder->getForm('\Drupal\oauth_login_oauth2\Form\MoOAuthRequestDemo');
      $response->addCommand(new OpenModalDialogCommand('Request 7-Days Full Feature Trial License', $modal_form, ['width' => '40%'] ) );
      return $response;
    }

    public function openSupportRequestForm() {
      $response = new AjaxResponse();
      $modal_form = $this->formBuilder->getForm('\Drupal\oauth_login_oauth2\Form\MoOAuthRequestSupport');
      $response->addCommand(new OpenModalDialogCommand('Support Request/Contact Us', $modal_form, ['width' => '40%'] ) );
      return $response;
    }

    public function app_configuration($name){
      $configFactory = \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings');
      $configFactory->set('miniorange_oauth_login_config_status', 'callback')->save();
      $path = Url::fromRoute('oauth_login_oauth2.config_clc',
        ['app_name' => $name])->toString();
      $configFactory->set('miniorange_oauth_login_config_application', $name)->save();

      $response = new RedirectResponse($path);
      $response->send();
      return $response;
    }

  /**
     * Initiating OAuth SSO flow
     */
    public function miniorange_oauth_client_mologin()
    {
        global $base_url;
        user_cookie_save(array("mo_oauth_test" => false));
        $enable_login = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_enable_login_with_oauth');

        if ($enable_login) {
          Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Login using SSO Enabled.');
            AuthorizationEndpoint::mo_oauth_client_initiateLogin();
            return new Response();
        }else {
          Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Login using SSO Disabled.');
            \Drupal::messenger()->addMessage(t('Please enable <b>Login with OAuth</b> to initiate the SSO.'), 'error');
            return new RedirectResponse($base_url);
        }
    }

    public function miniorange_oauth_client_update_email_username_attribute($data){
      $options = '';
      $selected_flag = 0;
      if (isset($data) && !empty($data)) {
        foreach ($data as $key => $value) {
          if ($selected_flag == 0 && ($key == 'email' || $key == 'email.0')) {
            $options = $options . '<option value="email" selected> email </option>';
            $selected_flag = 1;
          } elseif ($selected_flag == 0 && ($key == 'emails' || $key == 'emails.0')) {
            $options = $options . '<option value="emails" selected> emails </option>';
            $selected_flag = 1;
          } else {
            $options = $options . '<option value="' . $key . '"> ' . $key . ' </option>';
          }
        }
      }

      $html_string = '<p style="display: inline-block;">&emsp;<b> Email Attribute </b></p> &nbsp;&nbsp;&nbsp; <select id="mo_oauth_email_attribute" style="height: 32px;">' . $options . '</select>
                          &nbsp;&nbsp;&nbsp; <input style="display: none;" id="miniorange_oauth_client_other_field_for_email" placeholder="Enter Email Attribute">';

      echo $html_string. '';
      return new Response();
    }

    public function mo_post_testconfig(){
      $email_attr = $_GET['field_selected'];
      $config = \Drupal::config('oauth_login_oauth2.settings');
      $app_link = $config->get('miniorange_auth_client_display_link');
      \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_client_email_attr_val',$email_attr)->save();
      \Drupal::messenger()->addMessage(t('Configurations saved successfully. Please go to your Drupal site’s login page where you will automatically find a <b> ' . $app_link . ' </b>link.'));

      global $base_url;
      $response = new RedirectResponse($base_url."/admin/config/people/oauth_login_oauth2/config_clc");
      $response->send();
      return new Response();
    }

}
