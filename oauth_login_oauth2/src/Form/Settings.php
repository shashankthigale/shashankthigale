<?php

namespace Drupal\oauth_login_oauth2\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\oauth_login_oauth2\Utilities;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Console\Style\SymfonyStyle;

class Settings extends FormBase
{
    public function getFormId() {
        return 'miniorange_oauth_client_settings';
    }
/**
 * Showing Settings form.
 */
 public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;
    $baseUrlValue = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url');
    $url_path = $base_url . '/' . \Drupal::service('extension.list.module')->getPath('oauth_login_oauth2'). '/includes/Providers';

    $attachments['#attached']['library'][] = 'oauth_login_oauth2/oauth_login_oauth2.admin';

    $form['markup_library'] = array(
        '#attached' => array(
            'library' => array(
                "oauth_login_oauth2/oauth_login_oauth2.admin",
                "oauth_login_oauth2/oauth_login_oauth2.style_settings",
            )
        ),
    );

    $form['header_top_style_1'] = array('#markup' => '<div class="mo_oauth_table_layout_1">');

    $form['markup_top'] = array(
         '#markup' => '<div class="mo_oauth_table_layout mo_oauth_container">',
    );

    $form['markup_custom_sign_in'] = array(
        '#type' => 'fieldset',
        '#title' => t('SIGN IN SETTINGS'),
        '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
    );

    $form['markup_custom_sign_in']['miniorange_oauth_client_base_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Base URL: '),
        '#default_value' => $baseUrlValue,
        '#attributes' => array('id'=>'mo_oauth_vt_baseurl','style' => 'width:73%;','placeholder' => 'Enter Base URL'),
        '#description' => '<b>Note: </b>You can change your base/site URL from here. (For eg: https://www.xyz.com or http://localhost/abc)',
        '#suffix' => '<br>',
        '#prefix' => '<br><hr><br>',
    );

    $form['markup_custom_sign_in']['miniorange_oauth_client_siginin1'] = array(
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#attributes' => array('style' => 'margin: auto; display:block; '),
        '#value' => t('Update'),
    );

    $form['markup_custom_sign_in1'] = array(
        '#type' => 'fieldset',
        '#title' => t('ADVANCED SIGN IN SETTINGS <a href="licensing"><img class="mo_oauth_pro_icon1" src="' . $url_path . '/pro.png" alt="Premium and Enterprise"><span class="mo_pro_tooltip">Available in the Premium and Enterprise version</span></a><a class="mo_oauth_client_how_to_setup" href="https://developers.miniorange.com/docs/oauth-drupal/sign-in-settings#sign-in-settings-features" target="_blank">[What are Sign in settings feature]</a>'),
        '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
    );

    $form['markup_custom_sign_in1']['miniorange_oauth_force_auth'] = array(
        '#type' => 'checkbox',
        '#title' => t('Protect website against anonymous access'),
        '#disabled' => TRUE,
        '#prefix' => '<br><hr><br>',
        '#description' => t('<b>Note: </b>Users will be redirected to your OAuth server for login in case user is not logged in and tries to access website.<br><br>'),
    );

    $form['markup_custom_sign_in1']['miniorange_oauth_auto_redirect'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you want to <b> Auto-redirect to OAuth Provider/Server </b>'),
        '#disabled' => TRUE,
        '#description' => t('<b>Note: </b>Users will be redirected to your OAuth server for login when the login page is accessed.<br><br>'),
    );

    $form['markup_custom_sign_in1']['miniorange_oauth_enable_backdoor'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you want to enable <b>backdoor login </b>'),
        '#disabled' => TRUE,
        '#description' => t('<b>Note: </b>Checking this option creates a backdoor to login to your Website using Drupal credentials<br> incase you get locked out of your OAuth server.
                <br><b>Note down this URL: </b>Available in <a href="' . $base_url . '/admin/config/people/oauth_login_oauth2/licensing"><b>Premium, Enterprise</b></a> versions of the module.'),
    );

   $form['markup_custom_sign_in2'] = array(
        '#type' => 'fieldset',
       '#title' => t('DOMAIN & PAGE RESTRICTION <a href="licensing"><img class="mo_oauth_pro_icon1" src="' . $url_path . '/pro.png" alt="Enterprise"><span class="mo_pro_tooltip">Available in the Enterprise version</span></a><a class="mo_oauth_client_how_to_setup" href="https://developers.miniorange.com/docs/oauth-drupal/sign-in-settings#domain-restriction" target="_blank">[What is Domain and Page Restriction]</a>'),
       '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
   );

   $form['markup_custom_sign_in2']['miniorange_oauth_client_white_list_url'] = array(
     '#type' => 'textfield',
     '#title' => t('Allowed Domains'),
     '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated domains (Eg. xxxx.com; xxxx.com)'),
     '#description' => t('<b>Note: </b> Enter <b>semicolon(;) separated</b> domains to allow SSO. Other than these domains will not be allowed to do SSO.'),
     '#disabled' => TRUE,
     '#prefix' => '<br><hr><br>',
   );

   $form['markup_custom_sign_in2']['miniorange_oauth_client_black_list_url'] = array(
     '#type' => 'textfield',
     '#title' => t('Restricted Domains'),
     '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated domains (Eg. xxxx.com; xxxx.com)'),
     '#description' => t('<b>Note: </b> Enter <b>semicolon(;) separated</b> domains to restrict SSO. Other than these domains will be allowed to do SSO.'),
     '#disabled' => TRUE,
   );

   $form['markup_custom_sign_in2']['miniorange_oauth_client_page_restrict_url'] = array(
     '#type' => 'textfield',
     '#title' => t('Page Restriction'),
     '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated page URLs (Eg. xxxx.com/yyy; xxxx.com/yyy)'),
     '#description' => t('<b>Note: </b> Enter <b>semicolon(;) separated</b> URLs to restrict unauthorized access.'),
     '#disabled' => TRUE,
   );

   $form['markup_custom_sign_in2']['miniorange_oauth_client_siginin'] = array(
     '#type' => 'button',
     '#disabled' => TRUE,
     '#value' => t('Save Configuration'),
     '#button_type' => 'primary',
     '#attributes' => array('style' => '	margin: auto; display:block; '),
   );


   $form['markup_custom_login_button'] = array(
     '#type' => 'fieldset',
     '#title' => t('LOGIN BUTTON CUSTOMIZATION <a href="licensing"><img class="mo_oauth_pro_icon1" src="' . $url_path . '/pro.png" alt="Standard, Premium, Enterprise"><span class="mo_pro_tooltip">Available in the Standard, Premium and Enterprise version</span></a>'),
     '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
   );

   $form['markup_custom_login_button']['markup_top1'] = array(
     '#markup' => '<br><hr><br>',
   );

   $form['markup_custom_login_button']['miniorange_oauth_icon_width'] = array(
     '#type' => 'textfield',
     '#title' => t('Icon width'),
     '#disabled' => TRUE,
     '#description' => t('For eg.200px or 10% <br>'),
   );

   $form['markup_custom_login_button']['miniorange_oauth_icon_height'] = array(
     '#type' => 'textfield',
     '#title' => t('Icon height'),
     '#disabled' => TRUE,
     '#description' => t('For eg.60px or auto <br>'),
   );

   $form['markup_custom_login_button']['miniorange_oauth_icon_margins'] = array(
     '#type' => 'textfield',
     '#title' => t('Icon Margins'),
     '#disabled' => TRUE,
     '#description' => t('For eg. 2px 3px or auto <br>'),
   );

   $form['markup_custom_login_button']['miniorange_oauth_custom_css'] = array(
     '#type' => 'textarea',
     '#title' => t('Custom CSS'),
     '#disabled' => TRUE,
     '#attributes' => array('style'=> 'width:80%', 'placeholder' => 'For eg.  .oauthloginbutton{ background: #7272dc; height:40px; padding:8px; text-align:center; color:#fff; }'),
   );

   $form['markup_custom_login_button']['miniorange_oauth_btn_txt'] = array(
     '#type' => 'textfield',
     '#title' => t('Custom Button Text'),
     '#disabled' => TRUE,
     '#attributes' => array('placeholder'=> 'Login Using appname'),
   );

   $form['markup_custom_login_button']['mo_header_style_end'] = array('#markup' => '</div>');

   Utilities::schedule_a_meeting($form, $form_state);

    $form['mo_markup_div_imp']=array('#markup'=>'</div>');
    return $form;
 }

 public function submitForm(array &$form, FormStateInterface $form_state) {
   global $base_url;
    $baseUrlvalue = trim($form['markup_custom_sign_in']['miniorange_oauth_client_base_url']['#value']);
    if(!empty($baseUrlvalue) && filter_var($baseUrlvalue, FILTER_VALIDATE_URL) == FALSE) {
        \Drupal::messenger()->adderror(t('Please enter a valid URL'));
        return;
    }
   \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_client_base_url', $baseUrlvalue)->save();
   $miniorange_auth_client_callback_uri = !empty($baseUrlvalue) ? $baseUrlvalue."/mo_login" : $base_url."/mo_login";
   \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_auth_client_callback_uri',$miniorange_auth_client_callback_uri)->save();
   \Drupal::messenger()->addMessage(t('Configurations saved successfully.'));
 }

  public static function setup_call(array &$form, FormStateInterface $form_state){
    Utilities::schedule_a_call($form, $form_state);
  }

}
