<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\moUserProvisioningSupport;
use Drupal\user_provisioning\moUserProvisioningUtilities;
use Psr\Log\LoggerInterface;

class MoUserProvisioningRequestTrial extends FormBase
{
    private ImmutableConfig $config;
    protected $messenger;

    public function __construct()
    {
        $this->config = Drupal::config('user_provisioning.settings');
        $this->messenger = Drupal::messenger();
    }

    public function getFormId()
    {
        return 'user_provisioning_request_trial';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL)
    {

        $form['#prefix'] = '<div id="modal_example_form">';
        $form['#suffix'] = '</div>';
        $form['status_messages'] = [
            '#type' => 'status_messages',
            '#weight' => -10,
        ];

        $user_email = $this->config->get('user_provisioning_customer_admin_email');

        $form['mo_user_provisioning_trial_email_address'] = array(
            '#type' => 'email',
            '#title' => t('Email'),
            '#default_value' => $user_email,
            '#required' => true,
            '#attributes' => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
        );

        $form['mo_user_provisioning_trial_method'] = array(
            '#type' => 'select',
            '#title' => t('Trial Method'),
            '#attributes' => array('style' => 'width:99%;height:30px;margin-bottom:1%;'),
            '#options' => [
                'Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' SCIM Client' => t('Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' SCIM CLIENT'),
                'Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' SCIM SERVER' => t('Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' SCIM SERVER'),
                'Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' PROVIDER SPECIFIC PROVISIONING' => t('Drupal ' . moUserProvisioningUtilities::mo_get_drupal_core_version() . ' PROVIDER SPECIFIC PROVISIONING'),
                'Not Sure' => t('Not Sure (We will assist you further)'),
            ],
        );

        $form['mo_user_provisioning_trial_description'] = array(
            '#type' => 'textarea',
            '#rows' => 4,
            '#required' => true,
            '#title' => t('Description'),
            '#attributes' => array('placeholder' => t('Describe your use case here!'), 'style' => 'width:99%;'),
        );

        $form['mo_user_provisioning_trial_note'] = array(
            '#markup' => t('<div>If you are not sure what to choose, you can get in touch with us on <a href="mailto:' . moUserProvisioningConstants::SUPPORT_EMAIL . '">' . moUserProvisioningConstants::SUPPORT_EMAIL . '</a> and we will assist you further.</div>'),
        );

        $form['actions'] = array('#type' => 'actions');
        $form['actions']['send'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#attributes' => [
                'class' => [
                    'use-ajax',
                    'button--primary'
                ],
            ],
            '#ajax' => [
                'callback' => [$this, 'submitModalFormAjax'],
                'event' => 'click',
            ],
        ];

        $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
        return $form;
    }

    public function submitModalFormAjax(array $form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $response = new AjaxResponse();
        // If there are any form errors, AJAX replace the form.
        if ($form_state->hasAnyErrors()) {
            $response->addCommand(new ReplaceCommand('#modal_example_form', $form));
        } else {
            $email = $form_values['mo_user_provisioning_trial_email_address'];
            $query = $form_values['mo_user_provisioning_trial_method'] . ' : ' . $form_values['mo_user_provisioning_trial_description'];
            $query_type = 'Trial Request';

            $support = new moUserProvisioningSupport($email, '', $query, $query_type);
            $support_response = $support->sendSupportQuery();

            $this->messenger->addStatus(t('Success! Trial query successfully sent. We will provide you with the trial version shortly.'));
            $response->addCommand(new RedirectCommand(Url::fromRoute('user_provisioning.user_provisioning')->toString()));
        }
        return $response;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

}
