<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Psr\Log\LoggerInterface;

class MoAdvancedSettings extends FormBase
{
    private $base_url;
    private ImmutableConfig $config;
    private Config $config_factory;
    private LoggerInterface $logger;
    protected $messenger;

    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->config = Drupal::config('user_provisioning.settings');
        $this->config_factory = Drupal::configFactory()->getEditable('user_provisioning.settings');
        $this->logger = Drupal::logger('user_provisioning');
        $this->messenger = Drupal::messenger();
    }

    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return "mo_advanced_settings";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['mo_user_provision_add_css']=array(
            '#attached' => array(
                'library' => array(
                    'user_provisioning/user_provisioning.admin',
                )
            ),
        );

        $url = $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS;
        $mo_premium_tag = '<a href= "' . $url . '" >[PREMIUM]</a>';

        $form['header_top_style1'] = array(
            '#markup' => t('<div class="mo_user_provisioning_table_layout"><div class="mo_user_provisioning_container">
                               <div class="mo_user_provisioning_tab_heading">ADVANCED SETTINGS<hr></div>'),
        );


        /*
          * Build Form For Debug Logs
          */
        $form['mo_user_provision_logs'] = array(
          '#type' => 'details',
          '#title' => $this->t('Debug Logs'),
        );

        $form['mo_user_provision_logs']['mo_user_provisioning_enable_loggers'] = array(
          '#type' => 'checkbox',
          '#title' => $this->t('Enable loggers'),
          '#default_value' => $this->config->get('mo_user_provisioning_enable_loggers'),
          '#description' => $this->t('Enabling this checkbox will add the module logs under the default Drupal logger. Please note that these are development logs and are not the same as the logs that you see under the <b>Audit and Logs</b> tab.<br>
                                          <br><b>If you are facing any issues in our module, please follow the below steps:</b>
                                          <ol>
                                              <li>Please check the Enable Loggers checkbox and click on the Save button below.</li>
                                              <li>Now test the flow in which you are facing any issues.</li>
                                              <li>You can view these logs from under the <b>Reports -> Recent Log Messages</b> section of your Drupal site.</li>
                                              <li>Please send us the screenshot of the logs at <a href="mailto:drupalsupport@xecurify.com">drupalsupport@xecurify.com</a> so that we can debug and resolve the issue that you are facing.</li>
                                          </ol>   
                                       '),
        );

        $form['mo_user_provision_logs']['save_log_and_debug_config'] = array(
          '#type' => 'submit',
          '#value' => t('Save'),
          '#button_type' => 'primary',
          '#submit'=>array('::saveLogAndDebugConfig'),
          '#suffix' => '<br><br>',
        );
        /*
        * Import Export Users
        */
        $form['mo_user_provision_import_export_details']=array(
            '#type'=>'details',
            '#title'=> t('Import/Export Users'),
            '#open' => TRUE,
            '#attribute' => array('class'=>array('mo_user_provisioning_details_css')),
        );

        //Export users
        $form['mo_user_provision_import_export_details']['markup_top'] = array(
            '#markup' => '<br><strong>Export Users</strong><hr/>'
        );

        $form['mo_user_provision_import_export_details']['markup_1'] = array(
            '#markup' => '<br><div class="mo_user_provisioning_highlight_background_note"><p><strong>Note:</strong> This section will help you to export your users. Click on the below button to export users.</div>',
        );

        $form['mo_user_provision_import_export_details']['export_button']=array(
            '#markup'=> '<a class="use-ajax"
            data-dialog-type="modal"
            data-dialog-options="{&quot;width&quot;:800}"
            href="'.$this->base_url.'/admin/config/people/user_provisioning/MoUserProvisioningUserExport"><p class="button button--primary">Export</p></a>',
        );

        //Import users
        $form['mo_user_provision_import_export_details']['markup_import'] = array(
            '#markup' => $this->t('<br/><br/><br/><strong>Import Users '. $mo_premium_tag .'</strong><hr/><p>The below section will help you to import users and their fields.</p>
                  <p>Choose <b>"json"</b> or <b>"csv"</b>  exported user file and upload by clicking on the button given below. </p><br/>')
        );

        $form['mo_user_provision_import_export_details']['import_Config_file'] = array(
            '#type' => 'file',
            '#title' => t('Upload Exported User File'),
            '#disabled' => true,
        );

        $form['mo_user_provision_import_export_details']['import_button'] = array(
            '#type' => 'submit',
            '#value' => t('Import'),
            '#button_type' => 'primary',
            '#disabled' => true,
            '#submit'=>array('::userProvisioningImportUsers'),
            '#suffix' => '<br><br>',
        );


        //Debugger section ends here
        $form['mo_user_provisioning_div_close']=array(
            '#markup'=>'<br><br></div></div>',
        );

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }

    /**
     * This function read the given json or csv file
     * @return void
     */
    public function userProvisioningImportUsers() {
    }


    /**
     * Submit handler for logging and debugging
     * @param array $form
     * @param FormStateInterface $formState
     * @return void
     */
    public function saveLogAndDebugConfig(array &$form, FormStateInterface $formState){
        $this->config_factory->set('mo_user_provisioning_enable_loggers', $formState->getValue('mo_user_provisioning_enable_loggers'))->save();
        $this->messenger->addMessage('Logging and Debugger configuration saved successfully.');
    }
}
