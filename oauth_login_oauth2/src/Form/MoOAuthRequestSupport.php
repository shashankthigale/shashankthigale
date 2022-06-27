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

class MoOAuthRequestSupport extends FormBase
{
    public function getFormId() {
        return 'oauth_login_oauth2_request_support';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
        $form['#prefix'] = '<div id="modal_support_form">';
        $form['#suffix'] = '</div>';
        $form['status_messages'] = [
            '#type' => 'status_messages',
            '#weight' => -10,
        ];

        $form['markup_library'] = array(
          '#attached' => array(
            'library' => array(
              "oauth_login_oauth2/oauth_login_oauth2.module",
            )
          ),
        );

        $user_email = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_admin_email');
        $phone = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_admin_phone');

        $form['markup_1'] = array(
          '#markup' => t('<p class="mo_oauth_client_highlight_background_note_2">Need any help? We can help you with configuring miniOrange OAuth Login module on your site. Just send us a query and we will get back to you soon.</p>'),
        );
        $form['mo_oauth_support_email_address'] = array(
          '#type' => 'email',
          '#title' => t('Email'),
          '#default_value' => $user_email,
          '#required' => true,
          '#attributes' => array('placeholder' => t('Enter your email'), 'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['mo_oauth_support_phone_number'] = array(
          '#type' => 'textfield',
          '#title' => t('Phone'),
          '#default_value' => $phone,
          '#attributes' => array('placeholder' => t('Enter number with country code Eg. +00xxxxxxxxxx'), 'style' => 'width:99%;margin-bottom:1%;'),
        );
        $form['mo_oauth_support_query'] = array(
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

    public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
        $response = new AjaxResponse();
        // If there are any form errors, AJAX replace the form.
        if ( $form_state->hasAnyErrors() ) {
            $response->addCommand(new ReplaceCommand('#modal_support_form', $form));
        } else {
          $email = $form['mo_oauth_support_email_address']['#value'];
          $phone = $form['mo_oauth_support_phone_number']['#value'];
          $query = $form['mo_oauth_support_query']['#value'];
          $query_type = 'Support';

          $support = new MiniorangeOAuthClientSupport($email, $phone, $query, $query_type);
          $support_response = $support->sendSupportQuery();

          \Drupal::messenger()->addStatus(t('Support query successfully sent. We will get back to you shortly.'));
           $response->addCommand(new RedirectCommand(Url::fromRoute('oauth_login_oauth2.config_clc')->toString()));
        }
        return $response;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) { }

    public function submitForm(array &$form, FormStateInterface $form_state) { }

}
