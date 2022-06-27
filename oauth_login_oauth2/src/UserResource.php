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
class UserResource
{
  public static function getResourceOwner($resource_owner_details_url, $access_token)
  {
    Utilities::addLogger(basename(__FILE__), __FUNCTION__, __LINE__, 'Userinfo flow initiated.');

    $response = Utilities::callService($resource_owner_details_url,
      NULL,
      array('Authorization' => 'Bearer ' . $access_token),
      'GET'
    );

    if (isset($response) && !empty($response)) {
      $content = json_decode($response, true);
      Utilities::addLogger(basename(__FILE__), __FUNCTION__, __LINE__, 'Userinfo Content: <pre><code>' . print_r($content, true) . '</code></pre>');

      if (isset($content["error"]) || isset($content["error_description"])) {
        if (isset($content["error"]) && is_array($content["error"])) {
          $content["error"] = $content["error"]["message"];
        }
        Utilities::show_error_message($content);
      }
      return $content;
    }
    return null;
  }

}
