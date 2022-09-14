<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user_provisioning\Controller\user_provisioningController;
use Drupal\user_provisioning\moUserProvisioningUtilities;
use Psr\Log\LoggerInterface;

class MoUserProvisioningUserExport extends FormBase
{
    private $base_url;
    private ImmutableConfig $config;
    private Config $config_factory;
    private LoggerInterface $logger;

    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->config = Drupal::config('user_provisioning.settings');
        $this->config_factory = Drupal::configFactory()->getEditable('user_provisioning.settings');
        $this->logger = Drupal::logger('user_provisioning');
    }

    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return "mo_export";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['mo_user_provision_add_css'] = array(
            '#attached' => array(
                'library' => array(
                    'user_provisioning/user_provisioning.admin',
                    'core/drupal.dialog.ajax',
                )
            ),
        );

        $form['markup_top'] = array(
            '#markup' => '<!--<br><br>--><div class="mo_user_provisioning_sp_font_for_sub_heading"><strong>Export Users</strong></div><hr/><p>
                                Select the users attributes that you want to export.</p><div id="tester"></div>'
        );

        $user_attribute = array('name', 'mail', 'uid', 'uuid', 'langcode', 'roles', 'status', 'timezone', 'init', 'default_langcode', 'preferred_langcode', 'status');

        $user_attribute = moUserProvisioningUtilities::customUserFields();
        unset($user_attribute['']);
        $user_attribute = array_keys($user_attribute);

        $form['mo_user_provisioning_export_attribute'] = array(
            '#type' => 'table',
            '#attributes' => array('style' => 'border-collapse: separate;'),
        );

        $count = 0;
        for ($i = 0; $i < (int)sizeof($user_attribute) / 3; $i++) {
            if ($i * 3 + 1 <= sizeof($user_attribute)) {
                $form['mo_user_provisioning_export_attribute'][$i][$user_attribute[$count]] = array(
                    '#type' => 'checkbox',
                    '#disabled' => $user_attribute[$count] != 'name' && $user_attribute[$count] != 'mail',
                    '#default_value' => $user_attribute[$count] == 'name' || $user_attribute[$count] == 'mail',
                    '#title' => $user_attribute[$count++],
                );
            }
            if ($i * 3 + 2 <= sizeof($user_attribute)) {
                $form['mo_user_provisioning_export_attribute'][$i][$user_attribute[$count]] = array(
                    '#type' => 'checkbox',
                    '#disabled' => $user_attribute[$count] != 'name' && $user_attribute[$count] != 'mail',
                    '#default_value' => $user_attribute[$count] == 'name' || $user_attribute[$count] == 'mail',
                    '#title' => $user_attribute[$count++],
                );
            }
            if ($i * 3 + 3 <= sizeof($user_attribute)) {
                $form['mo_user_provisioning_export_attribute'][$i][$user_attribute[$count]] = array(
                    '#type' => 'checkbox',
                    '#disabled' => $user_attribute[$count] != 'name' && $user_attribute[$count] != 'mail',
                    '#default_value' => $user_attribute[$count] == 'name' || $user_attribute[$count] == 'mail',
                    '#title' => $user_attribute[$count++],
                );
            }
        }

        $form['mo_user_provisioning_file_type_option'] = array(
            '#type' => 'radios',
            '#title' => t("Please select file extension you want to download"),
            '#attributes' => array("style" => "display:inline"),
            '#default_value' => 'json',
            '#options' => array('json' => t('.json'), 'csv' => t('.csv')),
        );

        $form['actions'] = array('#type' => 'actions');
        $form['actions']['send'] = [
            '#type' => 'submit',
            '#value' => $this->t('Export'),
            '#attributes' => [
                'class' => [
                    'use-ajax',
                    'button--primary'
                ],
            ],
            '#ajax' => [
                'callback' => [$this, 'submitForm'],
                'event' => 'click',
            ],
        ];

        $form['mo_user_provisioning_div_close'] = array(
            '#markup' => '<br><br></div>',
        );

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $response = new AjaxResponse();
        $command = new CloseModalDialogCommand();

        $form_values = $form_state->getValue('mo_user_provisioning_export_attribute');
        $file_extension = $form_state->getValue('mo_user_provisioning_file_type_option');

        $user_attribute = ['name' => 1, 'mail' => 1];

        if ($user_attribute['name'] == 0 && $user_attribute['mail'] == 0) {
            $response->addCommand(new InvokeCommand('#tester', 'css', array('color', "red")));//todo not showing the message
            $response->addCommand(new HtmlCommand('#tester', 'Please select at least one attribute'));
            return $response;
        }

        $this->config_factory->set('mo_user_provisioning_export_config', $user_attribute)->save();
        $this->config_factory->set('mo_user_provisioning_file_extension', $file_extension)->save();

        $response->addCommand($command);
        $response->addCommand(new RedirectCommand(Url::fromRoute('user_provisioning.export_users',)->toString()));

        return $response;

    }
}
