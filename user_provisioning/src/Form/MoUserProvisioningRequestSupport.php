<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user_provisioning\moUserProvisioningSupport;

class MoUserProvisioningRequestSupport extends FormBase
{
    private ImmutableConfig $config;
    protected $messenger;

    public function __construct()
    {
        global $base_url;
        $this->config = Drupal::config('user_provisioning.settings');
        $this->messenger = Drupal::messenger();
    }

    public function getFormId()
    {
        return 'mo_user_provisioning_request_support';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL)
    {
        $form['#prefix'] = '<div id="modal_support_form">';
        $form['#suffix'] = '</div>';
        $form['status_messages'] = [
            '#type' => 'status_messages',
            '#weight' => -10,
        ];

        $form['markup_library'] = array(
            '#attached' => array(
                'library' => array(
                    "user_provisioning/user_provisioning.admin",
                )
            ),
        );

        $user_email = $this->config->get('mo_user_provisioning_customer_email');
        $phone = $this->config->get('mo_user_provisioning_customer_phone');

        $form['markup_1'] = array(
            '#markup' => t('<p class="mo_user_provisioning_background_note">Need any help? We can help you with configuring miniOrange User Provisioning module on your site. Just send us a query and we will get back to you soon.</p>'),
        );
        $form['mo_user_provisioning_support_email_address'] = array(
            '#type' => 'email',
            '#title' => t('Email'),
            '#default_value' => $user_email,
            '#required' => true,
            '#attributes' => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['mo_user_provisioning_support_phone_number'] = array(
            '#type' => 'textfield',
            '#title' => t('Phone'),
            '#default_value' => $phone,
            '#attributes' => array('placeholder' => t('Enter number with country code Eg. +00xxxxxxxxxx'), 'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['mo_user_provisioning_support_query'] = array(
            '#type' => 'textarea',
            '#required' => true,
            '#title' => t('Query'),
            '#attributes' => array('placeholder' => t('Describe your query here!'), 'style' => 'width:99%'),
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
            $response->addCommand(new ReplaceCommand('#modal_support_form', $form));
        } else {
            $email = $form_values['mo_user_provisioning_support_email_address'];
            $phone = $form_values['mo_user_provisioning_support_phone_number'];
            $query = $form_values['mo_user_provisioning_support_query'];
            $query_type = 'Support';

            $support = new moUserProvisioningSupport($email, $phone, $query, $query_type);
            $support_response = $support->sendSupportQuery();

            $this->messenger->addStatus(t('Support query successfully sent. We will get back to you shortly.'));
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
