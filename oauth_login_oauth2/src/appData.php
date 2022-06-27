<?php
namespace Drupal\oauth_login_oauth2;
use Drupal\Core\Render\Markup;

class appData{

  public static function app_list($temp){

    global $base_url;

    $url_path = $base_url . '/' . \Drupal::service('extension.list.module')->getPath('oauth_login_oauth2'). '/includes/Providers';

    if ($temp == 'oauth_apps') {
        return array(
          Markup::create('<li class="mo-flex-item" id="azure"><a class="mo-apps-table-text"  href="configure_app/azure"><img class="img-mo-logo" src="' . $url_path . '/azure.png">  <br>Azure AD </a>'),
          Markup::create('<li class="mo-flex-item" id="office365"><a class="mo-apps-table-text"  href="configure_app/office365"><img class="img-mo-logo" src="' . $url_path . '/office365.png"> <br> Office 365 </a> '),
          Markup::create('<li class="mo-flex-item" id="google"><a class="mo-apps-table-text"  href="configure_app/google"><img class="img-mo-logo" src="' . $url_path . '/google.png">  <br> Google </a>'),
          Markup::create('<li class="mo-flex-item" id="keycloak"><a class="mo-apps-table-text"  href="configure_app/keycloak"><img class="img-mo-logo" src="' . $url_path . '/keycloak.png"> <br> Keycloak </a>'),
          Markup::create('<li class="mo-flex-item" id="okta"><a class="mo-apps-table-text"  href="configure_app/okta"><img class="img-mo-logo" src="' . $url_path . '/okta.png"> <br> Okta </a> '),
          Markup::create('<li class="mo-flex-item" id="facebook"><a class="mo-apps-table-text"  href="configure_app/facebook"><img class="img-mo-logo" src="' . $url_path . '/facebook.png">  <br> Facebook </a>'),
          Markup::create('<li class="mo-flex-item" id="salesforce"><a class="mo-apps-table-text"  href="configure_app/salesforce"><img class="img-mo-logo" src="' . $url_path . '/salesforce.png"> <br> Salesforce </a> '),
          Markup::create('<li class="mo-flex-item" id="discord"><a class="mo-apps-table-text"  href="configure_app/discord"><img class="img-mo-logo" src="' . $url_path . '/discord.png">  <br> Discord </a>'),
          Markup::create('<li class="mo-flex-item" id="github"><a class="mo-apps-table-text"  href="configure_app/github"><img class="img-mo-logo" src="' . $url_path . '/github.png"> <br> GitHub </a> '),
          Markup::create('<li class="mo-flex-item" id="whmcs"><a class="mo-apps-table-text"  href="configure_app/whmcs"><img class="img-mo-logo" src="' . $url_path . '/whmcs.png"> <br> WHMCS </a> '),
          Markup::create('<li class="mo-flex-item" id="wildApricot"><a class="mo-apps-table-text"  href="configure_app/wildApricot"><img class="img-mo-logo" src="' . $url_path . '/wildApricot.png"> <br> Wild Apricot </a> '),
          Markup::create('<li class="mo-flex-item" id="zoho"><a class="mo-apps-table-text"  href="configure_app/zoho"><img class="img-mo-logo" src="' . $url_path . '/zoho.png"> <br> ZOHO </a> '),
          Markup::create('<li class="mo-flex-item" id="reddit"><a class="mo-apps-table-text"  href="configure_app/reddit"><img class="img-mo-logo" src="' . $url_path . '/reddit.png"> <br> Reddit </a> '),
          Markup::create('<li class="mo-flex-item" id="miniorange"><a class="mo-apps-table-text"  href="configure_app/miniorange"><img class="img-mo-logo" src="' . $url_path . '/miniorange.png">  <br> miniOrange </a>'),
          Markup::create('<li class="mo-flex-item" id="autodesk"><a class="mo-apps-table-text"  href="configure_app/autodesk"><img class="img-mo-logo" src="' . $url_path . '/autodesk.png"> <br> Autodesk </a>'),
          Markup::create('<li class="mo-flex-item" id="bitrix24"><a class="mo-apps-table-text"  href="configure_app/bitrix24"><img class="img-mo-logo" src="' . $url_path . '/bitrix24.png">  <br> Bitrix24 </a>'),
          Markup::create('<li class="mo-flex-item" id="blizzard"><a class="mo-apps-table-text"  href="configure_app/blizzard"><img class="img-mo-logo" src="' . $url_path . '/blizzard.png">  <br> Blizzard </a>'),
          Markup::create('<li class="mo-flex-item" id="box"><a class="mo-apps-table-text"  href="configure_app/box"><img class="img-mo-logo" src="' . $url_path . '/box.png">  <br> Box </a>'),
          Markup::create('<li class="mo-flex-item" id="canvas"><a class="mo-apps-table-text"  href="configure_app/canvas"><img class="img-mo-logo" src="' . $url_path . '/canvas.png"> <br> Canvas </a> '),
          Markup::create('<li class="mo-flex-item" id="classlink"><a class="mo-apps-table-text"  href="configure_app/classlink"><img class="img-mo-logo" src="' . $url_path . '/classlink.png"> <br> Classlink </a>'),
          Markup::create('<li class="mo-flex-item" id="clever"><a class="mo-apps-table-text"  href="configure_app/clever"><img class="img-mo-logo" src="' . $url_path . '/clever.png">  <br> Clever </a>'),
          Markup::create('<li class="mo-flex-item" id="coil"><a class="mo-apps-table-text"  href="configure_app/coil"><img class="img-mo-logo" src="' . $url_path . '/coil.png">  <br> Coil </a>'),
          Markup::create('<li class="mo-flex-item" id="connect2id"><a class="mo-apps-table-text"  href="configure_app/connect2id"><img class="img-mo-logo" src="' . $url_path . '/connect2id.png">  <br> Connect2id </a>'),
          Markup::create('<li class="mo-flex-item" id="dailymotion"><a class="mo-apps-table-text"  href="configure_app/dailymotion"><img class="img-mo-logo" src="' . $url_path . '/dailymotion.png">  <br> Dailymotion </a>'),
          Markup::create('<li class="mo-flex-item" id="devart"><a class="mo-apps-table-text"  href="configure_app/devart"><img class="img-mo-logo" src="' . $url_path . '/devart.png"> <br> Devianart </a> '),
          Markup::create('<li class="mo-flex-item" id="did"><a class="mo-apps-table-text"  href="configure_app/did"><img class="img-mo-logo" src="' . $url_path . '/did.png"> <br> DID App </a>'),
          Markup::create('<li class="mo-flex-item" id="eveonline"><a class="mo-apps-table-text"  href="configure_app/eveonline"><img class="img-mo-logo" src="' . $url_path . '/eveonline.png">  <br> Eve Online </a>'),
          Markup::create('<li class="mo-flex-item" id="fitbit"><a class="mo-apps-table-text"  href="configure_app/fitbit"><img class="img-mo-logo" src="' . $url_path . '/fitbit.png">  <br> FitBit </a>'),
          Markup::create('<li class="mo-flex-item" id="gitlab"><a class="mo-apps-table-text"  href="configure_app/gitlab"><img class="img-mo-logo" src="' . $url_path . '/gitlab.png"> <br> GitLab </a>'),
          Markup::create('<li class="mo-flex-item" id="gluu"><a class="mo-apps-table-text"  href="configure_app/gluu"><img class="img-mo-logo" src="' . $url_path . '/gluu.png">  <br> Gluu Server </a>'),
          Markup::create('<li class="mo-flex-item" id="identityserver"><a class="mo-apps-table-text"  href="configure_app/identityserver"><img class="img-mo-logo" src="' . $url_path . '/identityserver.png">  <br> Identity Server </a>'),
          Markup::create('<li class="mo-flex-item" id="intuit"><a class="mo-apps-table-text"  href="configure_app/intuit"><img class="img-mo-logo" src="' . $url_path . '/intuit.png">  <br> Intuit </a>'),
          Markup::create('<li class="mo-flex-item" id="invis"><a class="mo-apps-table-text"  href="configure_app/invis"><img class="img-mo-logo" src="' . $url_path . '/invis.png"> <br> Invision Community</a> '),
          Markup::create('<li class="mo-flex-item" id="laravel"><a class="mo-apps-table-text"  href="configure_app/laravel"><img class="img-mo-logo" src="' . $url_path . '/laravel.png">  <br> Laravel </a>'),
          Markup::create('<li class="mo-flex-item" id="linkedin"><a class="mo-apps-table-text"  href="configure_app/linkedin"><img class="img-mo-logo" src="' . $url_path . '/linkedin.png">  <br> LinkedIn </a>'),
          Markup::create('<li class="mo-flex-item" id="meetup"><a class="mo-apps-table-text"  href="configure_app/meetup"><img class="img-mo-logo" src="' . $url_path . '/meetup.png">  <br> Meetup </a>'),
          Markup::create('<li class="mo-flex-item" id="nextcloud"><a class="mo-apps-table-text"  href="configure_app/nextcloud"><img class="img-mo-logo" src="' . $url_path . '/nextcloud.png"> <br> Nextcloud </a> '),
          Markup::create('<li class="mo-flex-item" id="ping"><a class="mo-apps-table-text"  href="configure_app/ping"><img class="img-mo-logo" src="' . $url_path . '/ping.png"> <br> Ping </a> '),
          Markup::create('<li class="mo-flex-item" id="pinterest"><a class="mo-apps-table-text"  href="configure_app/pinterest"><img class="img-mo-logo" src="' . $url_path . '/pinterest.png"> <br> Pinterest </a> '),
          Markup::create('<li class="mo-flex-item" id="servicenow"><a class="mo-apps-table-text"  href="configure_app/servicenow"><img class="img-mo-logo" src="' . $url_path . '/servicenow.png"> <br> ServiceNow </a> '),
          Markup::create('<li class="mo-flex-item" id="slack"><a class="mo-apps-table-text"  href="configure_app/slack"><img class="img-mo-logo" src="' . $url_path . '/slack.png"> <br> Slack </a> '),
          Markup::create('<li class="mo-flex-item" id="spotify"><a class="mo-apps-table-text"  href="configure_app/spotify"><img class="img-mo-logo" src="' . $url_path . '/spotify.png"> <br> Spotify </a> '),
          Markup::create('<li class="mo-flex-item" id="strava"><a class="mo-apps-table-text"  href="configure_app/strava"><img class="img-mo-logo" src="' . $url_path . '/strava.png"> <br> Strava </a> '),
          Markup::create('<li class="mo-flex-item" id="timezynk"><a class="mo-apps-table-text"  href="configure_app/timezynk"><img class="img-mo-logo" src="' . $url_path . '/timezynk.png"> <br> Timezynk </a> '),
          Markup::create('<li class="mo-flex-item" id="twitch"><a class="mo-apps-table-text"  href="configure_app/twitch"><img class="img-mo-logo" src="' . $url_path . '/twitch.png"> <br> Twitch </a> '),
          Markup::create('<li class="mo-flex-item" id="vatsim"><a class="mo-apps-table-text"  href="configure_app/vatsim"><img class="img-mo-logo" src="' . $url_path . '/vatsim.png"> <br> VATSIM </a> '),
          Markup::create('<li class="mo-flex-item" id="vimeo"><a class="mo-apps-table-text"  href="configure_app/vimeo"><img class="img-mo-logo" src="' . $url_path . '/vimeo.png"> <br> Vimeo </a> '),
          Markup::create('<li class="mo-flex-item" id="vimeo"><a class="mo-apps-table-text"  href="configure_app/vimeo"><img class="img-mo-logo" src="' . $url_path . '/vk.png"> <br> VKontakte </a> '),
          Markup::create('<li class="mo-flex-item" id="windowslive"><a class="mo-apps-table-text"  href="configure_app/windowslive"><img class="img-mo-logo" src="' . $url_path . '/windowslive.png"> <br> Windows Live </a> '),
          Markup::create('<li class="mo-flex-item" id="wordpress"><a class="mo-apps-table-text"  href="configure_app/wordpress"><img class="img-mo-logo" src="' . $url_path . '/wordpress.png"> <br> WordPress </a> '),
          Markup::create('<li class="mo-flex-item" id="wso2"><a class="mo-apps-table-text"  href="configure_app/wso2"><img class="img-mo-logo" src="' . $url_path . '/wso2.png"> <br> WSO2 </a> '),
          Markup::create('<li class="mo-flex-item" id="zendesk"><a class="mo-apps-table-text"  href="configure_app/zendesk"><img class="img-mo-logo" src="' . $url_path . '/zendesk.png"> <br> Zendesk </a> '),
        );
    }
    elseif ($temp == 'custom_oauth') {

        return array(
          Markup::create('<li class="mo-flex-item" id="oauth2"><a class="mo-apps-table-text"><img class="img-mo-logo" src="' . $url_path . '/oauth2.png" alt="oauth2"> <br> Custom OAuth 2.0 App </a>'),
          Markup::create('<li class="mo-flex-item disabled"><a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="OIDC App" src="' . $url_path . '/openid-connect.png"> <img class="mo_oauth_pro_icon" src="' . $url_path . '/pro.png"> <br> Custom OIDC App </a>'),
        );

    }elseif ($temp == 'oidc_apps') {

        return array(
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Azure AD B2C" src="' . $url_path . '/azure.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Azure AD B2C </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="AWS Cognito" src="' . $url_path . '/cognito.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> AWS Cognito </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Apple" src="' . $url_path . '/apple.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Apple </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Auth0" src="' . $url_path . '/auth0.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Auth0 </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Diaspora" src="' . $url_path . '/diaspora.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Diaspora </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Freja" src="' . $url_path . '/freja.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Freja eID </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="OneLogin" src="' . $url_path . '/onelogin.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> OneLogin </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="ORCID" src="' . $url_path . '/orcid.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> ORCID </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="PayPal" src="' . $url_path . '/paypal.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> PayPal </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Swiss RX Login" src="' . $url_path . '/swiss-rx-login.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Swiss RX Login </a>'),
          Markup::create('<a class="mo-apps-table-text" ><img class="img-mo-logo mo_oauth_list_disabled" alt="Yahoo" src="' . $url_path . '/yahoo.png"><img class="mo_oauth_pro_icon" alt="premium and Enterprise" src="' . $url_path . '/pro.png"> <br> Yahoo </a>'),
        );
    }else{
      return '';
    }
  }

  public static function app_guides($app_name){

    $guides = array(
      'autodesk' => ['setup' => 'https://plugins.miniorange.com/autodesk-sso-login-with-drupal-oauth-client', 'video' => ''],
      'azure' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/microsoft-azure-ad-sso-login', 'video' => 'https://www.youtube.com/watch?v=vqE5yED3Gu0'],
      'bitrix24' => ['setup' => 'https://plugins.miniorange.com/configure-bitrix24-oauthopenid-connect-server-drupal-8', 'video' => ''],
      'blizzard' => ['setup' => 'https://plugins.miniorange.com/blizzard-single-sign-on-sso-login-with-drupal-oauth-client', 'video' => ''],
      'canvas' => ['setup' => '', 'video' => ''],
      'classlink' => ['setup' => '', 'video' => ''],
      'clever' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/clever-sso-login', 'video' => ''],
      'coil' => ['setup' => 'https://plugins.miniorange.com/coil-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'connect2id' => ['setup' => 'https://plugins.miniorange.com/connect2id-sso-login-with-drupal-oauth-client', 'video' => ''],
      'dailymotion' => ['setup' => 'https://plugins.miniorange.com/dailymotion-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'devart' => ['setup' => 'https://plugins.miniorange.com/deviantart-sso-login-with-drupal-oauth-client', 'video' => ''],
      'did' => ['setup' => '', 'video' => ''],
      'discord' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/discord-sso-login', 'video' => 'https://www.youtube.com/watch?v=ANTbCMOvaE8'],
      'eveonline' => ['setup' => 'https://plugins.miniorange.com/eve-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'facebook' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/facebook-sso-login', 'video' => ''],
      'fitbit' => ['setup' => 'https://plugins.miniorange.com/configure-fitbit-oauth-server-for-drupal-8', 'video' => ''],
      'github' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/github-sso-login', 'video' => 'https://youtu.be/FzGH3EMnwws'],
      'gitlab' => ['setup' => 'https://plugins.miniorange.com/gitlab-sso-login-with-drupal-oauth-client', 'video' => ''],
      'gluu' => ['setup' => 'https://plugins.miniorange.com/gluu-sso-login-with-drupal-oauth-client', 'video' => ''],
      'google' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/google-sso-login', 'video' => 'https://www.youtube.com/watch?v=oc44XF73rZQ'],
      'identityserver' => ['setup' => 'https://plugins.miniorange.com/identityserver4-sso-login-with-drupal-oauth-client', 'video' => ''],
      'intuit' => ['setup' => 'https://plugins.miniorange.com/intuit-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'invis' => ['setup' => 'https://plugins.miniorange.com/invision-community-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'keycloak' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/keycloak-sso-login', 'video' => ''],
      'laravel' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/laravel-sso-login', 'video' => ''],
      'linkedin' => ['setup' => 'https://plugins.miniorange.com/configure-linkedin-as-an-oauth-openid-connect-server-for-drupal-8-client', 'video' => ''],
      'meetup' => ['setup' => '', 'video' => ''],
      'miniorange' => ['setup' => 'https://plugins.miniorange.com/guide-to-configure-miniorange-with-drupal', 'video' => ''],
      'nextcloud' => ['setup' => 'https://plugins.miniorange.com/nextcloud-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'office365' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/office-365-sso-login', 'video' => ''],
      'okta' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/okta-sso-login', 'video' => 'https://youtu.be/YGLRlJ9dxaU'],
      'ping' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/pingfederate-sso-login', 'video' => ''],
      'pinterest' => ['setup' => 'https://plugins.miniorange.com/pinterest-single-sign-on-sso-with-drupal-oauth-client', 'video' => ''],
      'reddit' => ['setup' => 'https://plugins.miniorange.com/configure-reddit-oauthopenid-connect-server-drupal-8', 'video' => ''],
      'salesforce' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/salesforce-sso-login', 'video' => 'https://youtu.be/3QRHEIoLzYw'],
      'servicenow' => ['setup' => '', 'video' => ''],
      'slack' => ['setup' => 'https://plugins.miniorange.com/configure-slack-as-as-oauth-openid-connect-server-in-drupal', 'video' => ''],
      'spotify' => ['setup' => 'https://plugins.miniorange.com/spotify-single-sign-on-sso-login-with-drupal-oauth-client', 'video' => 'https://youtu.be/d2p8w3Zdnz4'],
      'strava' => ['setup' => '', 'video' => ''],
      'timezynk' => ['setup' => '', 'video' => ''],
      'twitch' => ['setup' => 'https://plugins.miniorange.com/twitch-single-sign-on-sso-login-with-drupal-oauth-client', 'video' => ''],
      'vatsim' => ['setup' => 'https://plugins.miniorange.com/vatsim-sso-login-with-drupal-oauth-client-drupal-sso-login', 'video' => ''],
      'vimeo' => ['setup' => 'https://plugins.miniorange.com/vimeo-single-sign-on-sso-login-with-drupal-oauth-client', 'video' => ''],
      'vk' => ['setup' => 'https://plugins.miniorange.com/vkontakte-single-sign-on-sso-login-with-drupal-oauth-client', 'video' => ''],
      'whmcs' => ['setup' => 'https://plugins.miniorange.com/guide-to-configure-whmcs-as-an-oauth-server-for-drupal', 'video' => ''],
      'wildApricot' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/wild-apricot-sso-login', 'video' => ''],
      'windowslive' => ['setup' => '', 'video' => ''],
      'wordpress' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/wordpress-sso-login', 'video' => ''],
      'wso2' => ['setup' => '', 'video' => ''],
      'zendesk' => ['setup' => 'https://www.drupal.org/docs/contributed-modules/drupal-oauth-oidc-login/zendesk-sso-login', 'video' => ''],
      'zoho' => ['setup' => '', 'video' => ''],
      'oauth2' => ['setup' => '', 'video' => ''],
    );

    return $guides[$app_name];
  }

  public static function endpoints($app_name){
    $endpoints = array(
      'autodesk' => ['Scope: ' => 'user:read user-profile:read', 'Authorization Endpoint: ' => 'https://developer.api.autodesk.com/authentication/v1/authorize', 'Access Token Endpoint: ' => 'https://developer.api.autodesk.com/authentication/v1/gettoken', 'Userinfo Endpoint: ' => 'https://developer.api.autodesk.com/userprofile/v1/users/@me'],
      'azure' => ['Scope: ' => 'openid email profile', 'Authorization Endpoint: ' => 'https://login.microsoftonline.com/{tenant-id}/oauth2/v2.0/authorize', 'Access Token Endpoint: ' => 'https://login.microsoftonline.com/{tenant-id}/oauth2/v2.0/token', 'Userinfo Endpoint: ' => 'https://graph.microsoft.com/oidc/userinfo'],
      'bitrix24' => ['Scope: ' => 'user', 'Authorization Endpoint: ' => 'https://[your-id].bitrix24.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://[your-id].bitrix24.com/oauth/token/', 'Userinfo Endpoint: ' => 'https://[your-id].bitrix24.com/rest/user.current.json?auth='],
      'blizzard' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://us.battle.net/oauth/authorize', 'Access Token Endpoint: ' => 'https://us.battle.net/oauth/token', 'Userinfo Endpoint: ' => 'https://us.battle.net/oauth/userinfo'],
      'canvas' => ['Scope: ' => 'openid profile', 'Authorization Endpoint: ' => 'https://{your-site-url}/login/oauth2/auth', 'Access Token Endpoint: ' => 'https://{your-site-url}/login/oauth2/token', 'Userinfo Endpoint: ' => 'https://{your-site-url}/api/v1/users/self'],
      'classlink' => ['Scope: ' => 'profile email', 'Authorization Endpoint: ' => 'https://launchpad.classlink.com/oauth2/v2/auth', 'Access Token Endpoint: ' => 'https://launchpad.classlink.com/oauth2/v2/token', 'Userinfo Endpoint: ' => 'https://nodeapi.classlink.com/v2/my/info'],
      'clever' => ['Scope: ' => 'read:students read:teachers read:user_id', 'Authorization Endpoint: ' => 'https://clever.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://clever.com/oauth/tokens', 'Userinfo Endpoint: ' => 'https://api.clever.com/v3.0/me'],
      'coil' => ['Scope: ' => 'openid email', 'Authorization Endpoint: ' => 'https://coil.com/oauth/auth', 'Access Token Endpoint: ' => 'https://coil.com/oauth/token', 'Userinfo Endpoint: ' => 'https://api.coil.com/user/info'],
      'connect2id' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://c2id.com/login', 'Access Token Endpoint: ' => 'https://{your-base-server-url}/token', 'Userinfo Endpoint: ' => 'https://{your-base-server-url}/userinfo'],
      'dailymotion' => ['Scope: ' => 'email', 'Authorization Endpoint: ' => 'https://www.dailymotion.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://api.dailymotion.com/oauth/token', 'Userinfo Endpoint: ' => 'https://api.dailymotion.com/user/me?fields=id,username,email,first_name,last_name'],
      'devart' => ['Scope: ' => 'browse', 'Authorization Endpoint: ' => 'https://www.deviantart.com/oauth2/authorize', 'Access Token Endpoint: ' => 'https://www.deviantart.com/oauth2/token', 'Userinfo Endpoint: ' => 'https://www.deviantart.com/api/v1/oauth2/user/profile'],
      'did' => ['Scope: ' => 'openid email', 'Authorization Endpoint: ' => 'https://auth.did.app/oidc/authorize', 'Access Token Endpoint: ' => 'https://auth.did.app/oidc/token', 'Userinfo Endpoint: ' => 'https://auth.did.app/oidc/userinfo'],
      'discord' => ['Scope: ' => 'identify email', 'Authorization Endpoint: ' => 'https://discordapp.com/api/oauth2/authorize', 'Access Token Endpoint: ' => 'https://discordapp.com/api/oauth2/token', 'Userinfo Endpoint: ' => 'https://discordapp.com/api/users/@me'],
      'eveonline' => ['Scope: ' => 'publicData', 'Authorization Endpoint: ' => 'https://login.eveonline.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://login.eveonline.com/oauth/token', 'Userinfo Endpoint: ' => 'https://esi.evetech.net/verify'],
      'facebook' => ['Scope: ' => 'public_profile email', 'Authorization Endpoint: ' => 'https://www.facebook.com/dialog/oauth', 'Access Token Endpoint: ' => 'https://graph.facebook.com/v2.8/oauth/access_token', 'Userinfo Endpoint: ' => 'https://graph.facebook.com/me/?fields=id,name,email,age_range,first_name,gender,last_name,link'],
      'fitbit' => ['Scope: ' => 'profile', 'Authorization Endpoint: ' => 'https://www.fitbit.com/oauth2/authorize', 'Access Token Endpoint: ' => 'https://api.fitbit.com/oauth2/token', 'Userinfo Endpoint: ' => 'https://www.fitbit.com/1/user'],
      'github' => ['Scope: ' => 'user repo', 'Authorization Endpoint: ' => 'https://github.com/login/oauth/authorize', 'Access Token Endpoint: ' => 'https://github.com/login/oauth/access_token', 'Userinfo Endpoint: ' => 'https://api.github.com/user'],
      'gitlab' => ['Scope: ' => 'read_user', 'Authorization Endpoint: ' => 'https://gitlab.com/oauth/authorize', 'Access Token Endpoint: ' => 'http://gitlab.com/oauth/token', 'Userinfo Endpoint: ' => 'https://gitlab.com/api/v4/user'],
      'gluu' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'http://{gluu-server-domain}/oxauth/restv1/authorize', 'Access Token Endpoint: ' => 'http://{gluu-server-domain}/oxauth/restv1/token', 'Userinfo Endpoint: ' => 'http:///{gluu-server-domain}/oxauth/restv1/userinfo'],
      'google' => ['Scope: ' => 'email', 'Authorization Endpoint: ' => 'https://accounts.google.com/o/oauth2/auth', 'Access Token Endpoint: ' => 'https://www.googleapis.com/oauth2/v4/token', 'Userinfo Endpoint: ' => 'https://www.googleapis.com/oauth2/v1/userinfo'],
      'identityserver' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://{your-identityserver-domain}/connect/authorize', 'Access Token Endpoint: ' => 'https://{your-identityserver-domain}/connect/token', 'Userinfo Endpoint: ' => 'https://your-domain/connect/introspect'],
      'intuit' => ['Scope: ' => 'openid email profile', 'Authorization Endpoint: ' => 'https://appcenter.intuit.com/connect/oauth2', 'Access Token Endpoint: ' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer', 'Userinfo Endpoint: ' => 'https://accounts.platform.intuit.com/v1/openid_connect/userinfo'],
      'invis' => ['Scope: ' => 'email', 'Authorization Endpoint: ' => 'https://{invision-community-domain}/oauth/authorize/', 'Access Token Endpoint: ' => 'https://{invision-community-domain}/oauth/token/', 'Userinfo Endpoint: ' => 'https://{invision-community-domain}/api/core/me'],
      'keycloak' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => '{your-domain}/realms/{realm}/protocol/openid-connect/auth', 'Access Token Endpoint: ' => '{your-domain}/realms/{realm}/protocol/openid-connect/token', 'Userinfo Endpoint: ' => '{your-domain}/realms/{realm}/protocol/openid-connect/userinfo'],
      'laravel' => ['Scope: ' => '', 'Authorization Endpoint: ' => 'http://your-laravel-site-url/oauth/authorize', 'Access Token Endpoint: ' => 'http://your-laravel-site-url/oauth/token', 'Userinfo Endpoint: ' => 'http://your-laravel-site-url/api/user/get'],
      'linkedin' => ['Scope: ' => 'r_liteprofile r_emailaddress', 'Authorization Endpoint: ' => 'https://www.linkedin.com/oauth/v2/authorization', 'Access Token Endpoint: ' => 'https://www.linkedin.com/oauth/v2/accessToken', 'Userinfo Endpoint: ' => 'https://api.linkedin.com/v2/me'],
      'meetup' => ['Scope: ' => 'basic', 'Authorization Endpoint: ' => 'https://secure.meetup.com/oauth2/authorize', 'Access Token Endpoint: ' => 'https://secure.meetup.com/oauth2/access', 'Userinfo Endpoint: ' => 'https://api.meetup.com/members/self'],
      'miniorange' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://login.xecurify.com/moas/idp/openidsso', 'Access Token Endpoint: ' => 'https://login.xecurify.com/moas/rest/oauth/token', 'Userinfo Endpoint: ' => 'https://login.xecurify.com/moas/rest/oauth/getuserinfo'],
      'nextcloud' => ['Scope: ' => '', 'Authorization Endpoint: ' => 'https://{your-nextcloud-domain}/index.php/apps/oauth2/authorize', 'Access Token Endpoint: ' => 'https://{your-nextcloud-domain}/index.php/apps/oauth2/api/v1/token', 'Userinfo Endpoint: ' => 'https://{your-nextcloud-domain}/ocs/v2.php/cloud/user?format=json'],
      'office365' => ['Scope: ' => 'openid email profile', 'Authorization Endpoint: ' => 'https://login.microsoftonline.com/{tenant-id}/oauth2/v2.0/authorize', 'Access Token Endpoint: ' => 'https://login.microsoftonline.com/{tenant-id}/oauth2/v2.0/token', 'Userinfo Endpoint: ' => 'https://graph.microsoft.com/beta/me'],
      'okta' => ['Scope: ' => 'openid email profile', 'Authorization Endpoint: ' => 'https://{yourOktaDomain}.com/oauth2/default/v1/authorize', 'Access Token Endpoint: ' => 'https://{yourOktaDomain}.com/oauth2/default/v1/token', 'Userinfo Endpoint: ' => 'https://{yourOktaDomain}.com/oauth2/default/v1/userinfo'],
      'ping' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://{yourPingDomain}.com/as/authorization.oauth2', 'Access Token Endpoint: ' => 'https://{yourPingDomain}.com/as/token.oauth2', 'Userinfo Endpoint: ' => 'https://{yourPingDomain}.com/as/userinfo.openid'],
      'pinterest' => ['Scope: ' => 'read_public', 'Authorization Endpoint: ' => 'https://api.pinterest.com/oauth/', 'Access Token Endpoint: ' => 'https://api.pinterest.com/v1/oauth/token', 'Userinfo Endpoint: ' => 'https://api.pinterest.com/v1/me/'],
      'reddit' => ['Scope: ' => 'identity', 'Authorization Endpoint: ' => 'https://www.reddit.com/api/v1/authorize', 'Access Token Endpoint: ' => 'https://www.reddit.com/api/v1/access_token', 'Userinfo Endpoint: ' => 'https://www.reddit.com/api/v1/me'],
      'salesforce' => ['Scope: ' => 'email', 'Authorization Endpoint: ' => 'https://login.salesforce.com/services/oauth2/authorize', 'Access Token Endpoint: ' => 'https://login.salesforce.com/services/oauth2/token', 'Userinfo Endpoint: ' => 'https://login.salesforce.com/services/oauth2/userinfo'],
      'servicenow' => ['Scope: ' => '', 'Authorization Endpoint: ' => 'https://{your-servicenow-domain}/oauth_auth.do', 'Access Token Endpoint: ' => 'https://{your-servicenow-domain}/oauth_token.do', 'Userinfo Endpoint: ' => 'https://{your-servicenow-domain}/{rest-api-path}?access_token='],
      'slack' => ['Scope: ' => 'user-read-private user-read-email', 'Authorization Endpoint: ' => 'https://accounts.spotify.com/authorize', 'Access Token Endpoint: ' => 'https://accounts.spotify.com/api/token', 'Userinfo Endpoint: ' => 'https://api.spotify.com/v1/me'],
      'spotify' => ['Scope: ' => 'user-read-private user-read-email', 'Authorization Endpoint: ' => 'https://accounts.spotify.com/authorize', 'Access Token Endpoint: ' => 'https://accounts.spotify.com/api/token', 'Userinfo Endpoint: ' => 'https://api.spotify.com/v1/me'],
      'strava' => ['Scope: ' => 'read', 'Authorization Endpoint: ' => 'https://www.strava.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://www.strava.com/oauth/token', 'Userinfo Endpoint: ' => 'https://www.strava.com/api/v3/athlete'],
      'timezynk' => ['Scope: ' => 'read:user', 'Authorization Endpoint: ' => 'https://api.timezynk.com/api/oauth2/v1/auth', 'Access Token Endpoint: ' => 'https://api.timezynk.com/api/oauth2/v1/token', 'Userinfo Endpoint: ' => 'https://api.timezynk.com/api/oauth2/v1/userinfo'],
      'twitch' => ['Scope: ' => 'user:read:email openid', 'Authorization Endpoint: ' => 'https://id.twitch.tv/oauth2/authorize?claims={"id_token":{"email":null,"email_verified":null,"picture":null, "preferred_username":null}}', 'Access Token Endpoint: ' => 'https://id.twitch.tv/oauth2/token', 'Userinfo Endpoint: ' => 'https://id.twitch.tv/oauth2/userinfo'],
      'vatsim' => ['Scope: ' => 'full_name email', 'Authorization Endpoint: ' => 'https://auth.vatsim.net/oauth/authorize', 'Access Token Endpoint: ' => 'https://auth.vatsim.net/oauth/token', 'Userinfo Endpoint: ' => 'https://auth.vatsim.net/api/user'],
      'vimeo' => ['Scope: ' => 'public', 'Authorization Endpoint: ' => 'https://api.vimeo.com/oauth/authorize', 'Access Token Endpoint: ' => 'https://api.vimeo.com/oauth/access_token', 'Userinfo Endpoint: ' => 'https://api.vimeo.com/me'],
      'vk' => ['Scope: ' => '', 'Authorization Endpoint: ' => 'https://oauth.vk.com/authorize', 'Access Token Endpoint: ' => 'https://oauth.vk.com/access_token', 'Userinfo Endpoint: ' => 'https://api.vk.com/method/users.get?fields=id,name,email,age_range,first_name,gender,last_name,link&access_token='],
      'whmcs' => ['Scope: ' => 'openid profile email', 'Authorization Endpoint: ' => 'https://{yourWHMCSdomain}/oauth/authorize.php', 'Access Token Endpoint: ' => 'https://{yourWHMCSdomain}/oauth/token.php', 'Userinfo Endpoint: ' => 'https://{yourWHMCSdomain}/oauth/userinfo.php'],
      'wildApricot' => ['Scope: ' => 'auto', 'Authorization Endpoint: ' => 'https://{your_account_url}/sys/login/OAuthLogin', 'Access Token Endpoint: ' => 'https://oauth.wildapricot.org/auth/token', 'Userinfo Endpoint: ' => 'https://api.wildapricot.org/v2.1/accounts/{account_id}/contacts/me'],
      'windowslive' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://login.live.com/oauth20_authorize.srf', 'Access Token Endpoint: ' => 'https://login.live.com/oauth20_token.srf', 'Userinfo Endpoint: ' => 'https://apis.live.net/v5.0/me'],
      'wordpress' => ['Scope: ' => 'openid', 'Authorization Endpoint: ' => 'https://{your-wp-site-url}/wp-json/moserver/authorize', 'Access Token Endpoint: ' => 'https://{your-wp-site-url}/wp-json/moserver/token', 'Userinfo Endpoint: ' => 'https://{your-wp-site-url}/wp-json/moserver/resource'],
      'wso2' => ['Scope: ' => '', 'Authorization Endpoint: ' => 'https://domain/oauth2/authorize', 'Access Token Endpoint: ' => 'https://domain/oauth2/token', 'Userinfo Endpoint: ' => 'https://domain/oauth2/userinfo'],
      'zendesk' => ['Scope: ' => 'read write', 'Authorization Endpoint: ' => 'https://{subdomain}.zendesk.com/oauth/authorizations/new', 'Access Token Endpoint: ' => 'https://{subdomain}.zendesk.com/oauth/tokens', 'Userinfo Endpoint: ' => 'https://{subdomain}.zendesk.com/api/v2/users'],
      'zoho' => ['Scope: ' => 'server.profile.READ', 'Authorization Endpoint: ' => 'https://accounts.zoho.in/oauth/v2/auth', 'Access Token Endpoint: ' => 'https://accounts.zoho.in/oauth/v2/token', 'Userinfo Endpoint: ' => 'https://accounts.zoho.com/oauth/user/info'],
    );

    return $endpoints[$app_name];
  }

}
