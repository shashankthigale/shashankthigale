<?php

namespace Drupal\oauth_login_oauth2\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\oauth_login_oauth2\Utilities;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoOAuthTroubleshoot extends FormBase
{
    public function getFormId() {
        return 'miniorange_oauth_client_troubleshoot';
    }
/**
 * Showing Settings form.
 */
 public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;
    $baseUrlValue = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_base_url');

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

    $form['markup_custom_troubleshoot'] = array(
        '#type' => 'fieldset',
        '#title' => t('DEBUGGING AND TROUBLESHOOT'),
        '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
    );

   $form['markup_custom_troubleshoot']['miniorange_oauth_client_enable_logging'] = array(
     '#type' => 'checkbox',
     '#title' => t('Enable Logging'),
     '#default_value' => \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_enable_logging'),
     '#description' => 'Enabling this checkbox will add loggers under the <a href="'.$base_url.'/admin/reports/dblog?type%5B%5D=oauth_login_oauth2" target="_blank">Reports</a> section',
     '#suffix' => '<br>',
     '#prefix' => '<br><hr><br>',
   );

   $form['markup_custom_troubleshoot']['miniorange_oauth_client_siginin1'] = array(
     '#type' => 'submit',
     '#button_type' => 'primary',
     '#value' => t('Save Configuration'),
   );

   $form['markup_custom_export_logs'] = array(
     '#type' => 'fieldset',
     '#title' => t('EXPORT MODULE LOGS'),
     '#attributes' => array( 'style' => 'padding:2% 2% 5%; margin-bottom:2%' ),
   );

   $form['markup_custom_export_logs']['miniorange_oauth_client_enable_logging'] = array(
     '#type' => 'submit',
     '#value' => t('Download Module Logs'),
     '#limit_validation_errors' => array(),
     '#submit' => array('::miniorange_module_logs'),
     '#prefix' => '<br><hr><br> Click on the button below to download module related logs.<br><br>',
   );

   $form['markup_custom_login_button']['mo_header_style_end'] = array('#markup' => '</div>');

   Utilities::schedule_a_meeting($form, $form_state);

    $form['mo_markup_div_imp']=array('#markup'=>'</div>');
    return $form;
 }

 public function submitForm(array &$form, FormStateInterface $form_state) {
   global $base_url;
   $form_values = $form_state->getValues();
   $enable_logs = $form_values['miniorange_oauth_client_enable_logging'];
   \Drupal::configFactory()->getEditable('oauth_login_oauth2.settings')->set('miniorange_oauth_client_enable_logging',$enable_logs)->save();
   \Drupal::messenger()->addMessage(t('Configurations saved successfully.'));
 }

  public static function setup_call(array &$form, FormStateInterface $form_state){
    Utilities::schedule_a_call($form, $form_state);
  }

  public static function mofilterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  public static function miniorange_module_logs(){

    $connection = \Drupal::database();

// Excel file name for download
    $fileName = "drupal_oauth_login_loggers_" . date('Y-m-d') . ".xls";

// Column names
    $fields = array('WID', 'UID', 'TYPE', 'MESSAGE', 'VARIABLES', 'SEVERITY', 'LINK', 'LOCATION', 'REFERER', 'HOSTNAME', 'TIMESTAMP');

// Display column names as first row
    $excelData = implode("\t", array_values($fields)) . "\n\n";

// Fetch records from database
    $query = $connection->query("SELECT * from {watchdog} WHERE type = 'oauth_login_oauth2' OR severity = 3")->fetchAll();

    foreach ($query as $row){
      $lineData = array($row->wid, $row->uid, $row->type, $row->message, $row->variables, $row->severity, $row->link, $row->location, $row->referer, $row->hostname, $row->timestamp);
      array_walk($lineData, array('self','mofilterData'));
      $excelData .= implode("\t", array_values($lineData)) . "\n";
    }

// Headers for download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
    echo $excelData;
    exit;
  }



}
