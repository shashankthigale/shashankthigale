<?php

namespace Drupal\oauth_login_oauth2\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\oauth_login_oauth2\MiniorangeOAuthClientConstants;
use Drupal\oauth_login_oauth2\MiniorangeOAuthClientSupport;
use Drupal\oauth_login_oauth2\Utilities;

class MoOAuthRequestDemo extends FormBase
{
    public function getFormId() {
        return 'oauth_login_oauth2_request_demo';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {


        $form['#prefix'] = '<div id="modal_example_form">';
        $form['#suffix'] = '</div>';
        $form['status_messages'] = [
            '#type' => 'status_messages',
            '#weight' => -10,
        ];

      $user_email = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_admin_email');

      $form['mo_oauth_trial_email_address'] = array(
        '#type' => 'email',
        '#title' => t('Email'),
        '#default_value' => $user_email,
        '#required' => true,
        '#attributes' => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
      );
      $form['mo_oauth_trial_plan'] = array(
        '#type' => 'select',
        '#title' => t('Trial Plan'),
        '#attributes' => array('style' => 'width:99%;height:30px;margin-bottom:1%;'),
        '#options' => [
          'Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Standard Module' => t('Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Standard Module'),
          'Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Premium Module' => t('Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Premium Module'),
          'Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Enterprise Module' => t('Drupal ' . Utilities::mo_get_drupal_core_version() . ' OAuth Enterprise Module'),
          'Not Sure' => t('Not Sure (We will assist you with the suitable plan)'),
        ],
      );

      $form['mo_oauth_trial_description'] = array(
        '#type' => 'textarea',
        '#rows' => 4,
        '#required' => true,
        '#title' => t('Description'),
        '#attributes' => array('placeholder' => t('Describe your use case here!'), 'style' => 'width:99%;'),
        '#suffix' => '<br>',
      );

      $form['markup_trial_note'] = array(
        '#markup' => t('<div>If you are not sure with which plan you should go with, get in touch with us on <a href="mailto:'.MiniorangeOAuthClientConstants::SUPPORT_EMAIL.'">'.MiniorangeOAuthClientConstants::SUPPORT_EMAIL.'</a> and we will assist you with the suitable plan.</div>'),
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

    public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
        $response = new AjaxResponse();
        // If there are any form errors, AJAX replace the form.
        if ( $form_state->hasAnyErrors() ) {
            $response->addCommand(new ReplaceCommand('#modal_example_form', $form));
        } else {
          $email = $form['mo_oauth_trial_email_address']['#value'];
          $query = $form['mo_oauth_trial_plan']['#value'] .' : '.$form['mo_oauth_trial_description']['#value'];
          $query_type = 'Trial Request';

          $support = new MiniorangeOAuthClientSupport($email, '', $query, $query_type);
          $support_response = $support->sendSupportQuery();

          \Drupal::messenger()->addStatus(t('Success! Trial query successfully sent. We will provide you with the trial version shortly.'));
          $response->addCommand(new RedirectCommand(Url::fromRoute('oauth_login_oauth2.config_clc')->toString()));
        }
        return $response;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) { }

    public function submitForm(array &$form, FormStateInterface $form_state) { }

}
