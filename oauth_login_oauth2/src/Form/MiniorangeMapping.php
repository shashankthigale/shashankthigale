<?php

/**
 * @file
 * Contains \Drupal\miniorange_oauth_client\Form\MiniorangeGeneralSettings.
 */

namespace Drupal\oauth_login_oauth2\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\oauth_login_oauth2\Utilities;
use Drupal\Core\Form\FormBase;
use Drupal\oauth_login_oauth2\MiniorangeOAuthClientSupport;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MiniorangeMapping extends FormBase{
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'miniorange_mapping';
  }
  public function buildForm(array $form, FormStateInterface $form_state){
    global $base_url;
    $url_path = $base_url . '/' . \Drupal::service('extension.list.module')->getPath('oauth_login_oauth2'). '/includes/Providers';

      $form['markup_library'] = array(
      '#attached' => array(
          'library' => array(
              "oauth_login_oauth2/oauth_login_oauth2.admin",
              "oauth_login_oauth2/oauth_login_oauth2.style_settings",
              "oauth_login_oauth2/oauth_login_oauth2.email_username_attribute",
          )
      ),
    );

    $form['header_top_style_1'] = array('#markup' => '<div class="mo_oauth_table_layout_1">');
    $form['markup_top'] = array(
        '#markup' => '<div class="mo_oauth_table_layout mo_oauth_container">',
    );

    $form['markup_top_vt_start2'] = array(
      '#type' => 'details',
      '#title' => t('BACKUP/IMPORT CONFIGURATIONS'),
    );

    $form['markup_top_vt_start2']['markup_1'] = array(
        '#markup' => '<br><div class="mo_oauth_client_highlight_background_note_1"><p><b>NOTE: </b>This tab will help you to transfer your module configurations when you change your Drupal instance.
                      <br>Example: When you switch from test environment to production.<br>Follow these 3 simple steps to do that:<br>
                      <br><strong>1.</strong> Download module configuration file by clicking on the Download Configuration button given below.
                      <br><strong>2.</strong> Install the module on new Drupal instance.<br><strong>3.</strong> Upload the configuration file in Import module Configurations section.<br>
                      <br><b>And just like that, all your module configurations will be transferred!</b></p></div><br><div id="Exort_Configuration"><h3>Backup/ Export Configuration &nbsp;&nbsp;</h3><hr/><p>
                      Click on the button below to download module configuration.</p>',
    );

    $form['markup_top_vt_start2']['miniorange_oauth_imo_option_exists_export'] = array(
        '#type' => 'submit',
        '#value' => t('Download Module Configuration'),
        '#limit_validation_errors' => array(),
        '#submit' => array('::miniorange_import_export'),
        '#suffix'=> '<br/><br/></div>',
    );

    $form['markup_top_vt_start2']['markup_prem_plan'] = array(
        '#markup' => '<div id="Import_Configuration"><br/><h3>Import Configuration</h3><hr><br>
                      <div class="mo_oauth_highlight_background_note_1"><b>Note: </b>Available in
                      <a href="' . $base_url . '/admin/config/people/oauth_login_oauth2/licensing"><strong>Standard, Premium and Enterprise</strong></a> versions of the module</div>',
    );

    $form['markup_top_vt_start2']['markup_import_note'] = array(
        '#markup' => '<p>This tab will help you to<span style="font-weight: bold"> Import your module configurations</span> when you change your Drupal instance.</p>
             <p>choose <b>"json"</b> Extened module configuration file and upload by clicking on the button given below. </p>',
    );

    $form['markup_top_vt_start2']['import_Config_file'] = array(
        '#type' => 'file',
        '#disabled' => TRUE,
    );

    $form['markup_top_vt_start2']['miniorange_oauth_import'] = array(
        '#type' => 'submit',
        '#value' => t('Upload'),
        '#disabled' => TRUE,
        '#suffix' => '<br><br></div>'
    );

    $form['markup_custom_attribute'] = array(
        '#type' => 'fieldset',
        '#title' => t('CUSTOM ATTRIBUTE MAPPING <a href="licensing"><img class="mo_oauth_pro_icon1" src="' . $url_path . '/pro.png" alt="Premium and Enterprise"><span class="mo_pro_tooltip">Available in the Standard, Premium and Enterprise version</span></a><a class="mo_oauth_client_how_to_setup" href="https://developers.miniorange.com/docs/oauth-drupal/mapping#attribute-mapping" target="_blank">[What is Attribute Mapping and How to Set up]</a>'),
        '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
    );

    $form['markup_custom_attribute']['attribute_mapping_info'] = array(
      '#markup' => '<br><hr><br><div class="mo_oauth_client_highlight_background_note_1">This feature allows you to map the user attributes from your OAuth server to the user attributes in Drupal.</div>',
    );

     $form['markup_custom_attribute']['miniorange_oauth_attr_name'] = array(
       '#type' => 'textfield',
       '#prefix' => '<div><table><tr><td>',
       '#suffix' => '</td>',
       '#id' => 'text_field',
       '#title' => t('OAuth Server Attribute Name'),
       '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Server Attribute Name'),
       '#required' => FALSE,
       '#disabled' => TRUE,
     );
    $form['markup_custom_attribute']['miniorange_oauth_server_name'] = array(
         '#type' => 'textfield',
         '#id' => 'text_field1',
         '#prefix' => '<td>',
       '#suffix' => '</td>',
         '#title' => t('Drupal Machine Name'),
         '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Drupal Machine Name'),
         '#required' => FALSE,
         '#disabled' => TRUE,
       );
       $form['markup_custom_attribute']['miniorange_oauth_add_name'] = array(
        '#prefix' => '<td>',
        '#suffix' => '</td>',
        '#type' => 'button',
        '#disabled' => 'true',
        '#attributes' => array('style' => 'background-color: lightgreen;color:white'),
        '#value' => '+',
       );
       $form['markup_custom_attribute']['miniorange_oauth_sub_name'] = array(
        '#prefix' => '<td>',
        '#suffix' => '</td></tr></table></div>',
        '#type' => 'button',
        '#disabled' => 'true',
        '#attributes' => array('style' => 'background-color: red;color:white'),
        '#value' => '-',
       );

    $form['markup_custom_role_mapping'] = array(
        '#type' => 'fieldset',
        '#title' => t('CUSTOM ROLE MAPPING <a href="licensing"><img class="mo_oauth_pro_icon1" src="' . $url_path . '/pro.png" alt="Premium and Enterprise"><span class="mo_pro_tooltip">Available in the Premium and Enterprise version</span></a><a class="mo_oauth_client_how_to_setup" href="https://developers.miniorange.com/docs/oauth-drupal/mapping#role-mapping" target="_blank">[What is Role Mapping and How to Set up]</a>'),
        '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
    );


    $form['markup_custom_role_mapping']['role_mapping_info'] = array(
      '#markup' => '<br><hr><br><div class="mo_oauth_client_highlight_background_note_1">This feature allows you to map OAuth Server roles/groups to below configured Drupal Role.</div>',
    );

    $form['markup_custom_role_mapping']['miniorange_disable_attribute'] = array(
        '#type' => 'checkbox',
        '#title' => t('Do not update existing user&#39;s role.'),
        '#disabled' => TRUE,
        '#prefix' => '<br>',
    );
    $form['markup_custom_role_mapping']['miniorange_oauth_disable_role_update'] = array(
      '#type' => 'checkbox',
      '#title' => t('Check this option if you do not want to update user role if roles not mapped. '),
    '#disabled' => TRUE,
    );
    $form['markup_custom_role_mapping']['miniorange_oauth_disable_autocreate_users'] = array(
      '#type' => 'checkbox',
      '#title' => t('Check this option if you want to enable <b>auto creation</b> of users if user does not exist. '),
      '#disabled' => TRUE,
    );
	$mrole= user_role_names($membersonly = TRUE);
      $drole = array_values($mrole);

      $form['markup_custom_role_mapping']['miniorange_oauth_default_mapping'] = array(
          '#type' => 'select',
          '#id' => 'miniorange_oauth_client_app',
          '#title' => t('Select default group for the new users'),
          '#options' => $mrole,
          '#attributes' => array('style' => 'width:73%;'),
          '#disabled' => TRUE,
      );

      foreach($mrole as $roles) {
          $rolelabel = str_replace(' ','',$roles);
          $form['markup_custom_role_mapping']['miniorange_oauth_role_' . $rolelabel] = array(
              '#type' => 'textfield',
              '#title' => t($roles),
              '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Semi-colon(;) separated Group/Role value for ' . $roles),
              '#disabled' => TRUE,
          );
      }

    $form['markup_custom_role_mapping']['markup_role_signin'] = array(
        '#markup' => '<div class="custom-login-logout mo_oauth_custom_login_logout"><br><strong>Custom Login/Logout (Optional)</strong><hr></div>'
    );

    $form['markup_custom_role_mapping']['miniorange_oauth_client_login_url'] = array(
        '#type' => 'textfield',
        '#id' => 'text_field2',
        '#required' => FALSE,
        '#disabled' => TRUE,
        '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Login URL'),
    );
    $form['markup_custom_role_mapping']['miniorange_oauth_client_logout_url'] = array(
        '#type' => 'textfield',
        '#id' => 'text_field3',
        '#required' => FALSE,
        '#disabled' => TRUE,
        '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Logout URL'),
    );
    $form['markup_custom_role_mapping']['markup_role_break'] = array(
        '#markup' => '<br>',
    );
    $form['markup_custom_role_mapping']['miniorange_oauth_client_attr_setup_button'] = array(
        '#type' => 'submit',
        '#value' => t('Save Configuration'),
        '#disabled' => TRUE,
        '#attributes' => array('style' => '	margin: auto; display:block; '),
    );

    $form['mo_header_style_end'] = array('#markup' => '</div>');
    Utilities::schedule_a_meeting($form, $form_state);
    $form['miniorange_idp_guide_link_end'] = array(
        '#markup' => '</div>',
    );

    Utilities::schedule_a_meeting($form, $form_state);
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  }



    public static function setup_call(array &$form, FormStateInterface $form_state){
      Utilities::schedule_a_call($form, $form_state);
    }

    function clear_attr_list(&$form,$form_state){
        \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->clear('miniorange_oauth_client_attr_list_from_server')->save();
        Utilities::show_attr_list_from_idp($form, $form_state);
    }


    public function miniorange_oauth_client_table_data_mapping($key, $value, $options, $config, $other_email_attr, $other_name_attr){

      if ($key == 'email_attr') {
        $row[$key] = [
          '#markup' => '<div class="mo-mapping-floating"><strong>Email Attribute: </strong>',
        ];

        $row[$key.'_select'] = [
          '#type' => 'select',
          '#id' => 'mo_oauth_email_attribute',
          '#default_value' => $config->get($value),
          '#options' => $options,
        ];

        $row['miniorange_oauth_client_email_attr'] = [
          '#type' => 'textfield',
          '#default_value' => $other_email_attr,
          '#id' => 'miniorange_oauth_client_other_field_for_email',
          '#attributes' => array('style' => 'display:none;','placeholder' => 'Enter Email Attribute'),
          '#prefix' => '<div class="mo_oauth_attr_mapping_select_element">',
          '#suffix' => '</div>',
        ];
      }else{
        $row[$key] = [
          '#markup' => '<div class="mo-mapping-floating"><strong>Username Attribute: </strong>',
        ];

        $row[$key.'_select'] = [
          '#type' => 'select',
          '#id' => 'mo_oauth_name_attribute',
          '#default_value' => $config->get($value),
          '#options' => $options,
        ];

        $row['miniorange_oauth_client_name_attr'] = [
          '#type' => 'textfield',
          '#default_value' => $other_name_attr,
          '#id' => 'miniorange_oauth_client_other_field_for_name',
          '#attributes' => array('style' => 'display:none;','placeholder' => 'Enter Username Attribute'),
          '#prefix' => '<div class="mo_oauth_attr_mapping_select_element">',
          '#suffix' => '</div>',
        ];
      }

      return $row;
    }

    function miniorange_import_export()
    {
      $tab_class_name = array(
        'OAuth Login Configuration' => 'mo_options_enum_client_configuration',
        'Attribute Mapping' => 'mo_options_enum_attribute_mapping',
        'Sign In Settings' => 'mo_options_enum_signin_settings'
      );

      $configuration_array = array();
      foreach($tab_class_name as $key => $value) {
        $configuration_array[$key] = self::mo_get_configuration_array($value);
      }

      $configuration_array["Version_dependencies"] = self::mo_get_version_informations();
      header("Content-Disposition: attachment; filename = miniorange_oauth_client_config.json");
      echo(json_encode($configuration_array, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
      exit;
    }

    function mo_get_configuration_array($class_name)
    {
      $class_object = Utilities::getVariableArray($class_name);
      $mo_array = array();
      foreach($class_object as $key => $value) {
        $mo_option_exists = \Drupal::config('oauth_login_oauth2.settings')->get($value);
        if($mo_option_exists) {
          $mo_array[$key] = $mo_option_exists;
        }
      }
      return $mo_array;
    }

    function mo_get_version_informations() {
      $array_version = array();
      $array_version["PHP_version"] = phpversion();
      $array_version["Drupal_version"] = \DRUPAL::VERSION;
      $array_version["OPEN_SSL"] = self::mo_oauth_is_openssl_installed();
      $array_version["CURL"] = self::mo_oauth_is_curl_installed();
      $array_version["ICONV"] = self::mo_oauth_is_iconv_installed();
      $array_version["DOM"] = self::mo_oauth_is_dom_installed();
      return $array_version;
    }

    function mo_oauth_is_openssl_installed() {
      if ( in_array( 'openssl', get_loaded_extensions() ) ) {
        return 1;
      } else {
        return 0;
      }
    }
    function mo_oauth_is_curl_installed() {
      if ( in_array( 'curl', get_loaded_extensions() ) ) {
        return 1;
      } else {
        return 0;
      }
    }
    function mo_oauth_is_iconv_installed() {
      if ( in_array( 'iconv', get_loaded_extensions() ) ) {
        return 1;
      } else {
        return 0;
      }
    }
    function mo_oauth_is_dom_installed() {
      if ( in_array( 'dom', get_loaded_extensions() ) ) {
        return 1;
      } else {
        return 0;
      }
    }
}
