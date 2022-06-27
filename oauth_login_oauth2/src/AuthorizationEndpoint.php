<?php
namespace Drupal\oauth_login_oauth2;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Utility;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Extension;
use Drupal\Component\Utility\Html;
use Drupal\oauth_login_oauth2\handler;
    class AuthorizationEndpoint{
        public static function mo_oauth_client_initiateLogin() {
          Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Login using SSO Initiated.');
            global $base_url;
            if (session_status() === PHP_SESSION_NONE) {
              session_start();
            }
            $config = \Drupal::config('oauth_login_oauth2.settings');
            $_SESSION['redirect_url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $base_url;
          Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Redirect URL set to: '. $_SESSION['redirect_url']);
            $app_name = $config->get('miniorange_auth_client_display_link');
            $client_id = $config->get('miniorange_auth_client_client_id');
            $client_secret = $config->get('miniorange_auth_client_client_secret');
            $scope = $config->get('miniorange_auth_client_scope');
            $authorizationUrl = $config->get('miniorange_auth_client_authorize_endpoint');
            $access_token_ep = $config->get('miniorange_auth_client_access_token_ep');
            $user_info_ep = $config->get('miniorange_auth_client_user_info_ep');

            if ($client_secret==NULL||$client_id==NULL||$scope==NULL||$authorizationUrl==NULL||$access_token_ep==NULL||$user_info_ep==NULL) {
              Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Configurations could not be found.');
                echo '<div style="font-family:Calibri;padding:0 3%;">';
                echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;"> ERROR</div>
                                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;"><p><strong>Error: </strong>OAuth Server configurations could not be found.</p>
                                    <p><strong>Possible Cause: </strong>You may have not configured the module completely.</p>
                                </div>
                                <div style="margin:3%;display:block;text-align:center;"></div>
                                <div style="margin:3%;display:block;text-align:center;">
                                    <form action="'.$base_url.'" method ="post">
                                        <input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;"type="submit" value="Done" onClick="self.close();">
                                    </form>
                                </div>';
                exit;
                return new Response();
            }

            if(!empty(\Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url')))
                $baseUrlValue = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url');
            else
                $baseUrlValue = $base_url;
            $callback_uri = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_auth_client_callback_uri');
            $state = base64_encode($app_name);
            if (strpos($authorizationUrl,'?') !== false)
                $authorizationUrl =$authorizationUrl. "&client_id=".$client_id."&scope=".$scope."&redirect_uri=".$callback_uri."&response_type=code&state=".$state;
            else
                $authorizationUrl =$authorizationUrl. "?client_id=".$client_id."&scope=".$scope."&redirect_uri=".$callback_uri."&response_type=code&state=".$state;
            if (session_status() == PHP_SESSION_NONE)
                session_start();

          Utilities::addLogger(basename(__FILE__),__FUNCTION__,__LINE__,'Authorization URL: '.$authorizationUrl);
            $_SESSION['oauth2state'] = $state;
            $response = new RedirectResponse($authorizationUrl);
            $response->send();
            return new Response();
        }
    }