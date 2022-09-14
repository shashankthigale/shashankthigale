<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\moUserProvisioningCustomer;
use Drupal\user_provisioning\moUserProvisioningUtilities;
use Psr\Log\LoggerInterface;

class MoUserProvisioningCustomerSetup extends FormBase
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

    public function getFormId()
    {
        return "mo_user_provisioning_customer_setup";
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['mo_user_provision_add_css'] = [
            '#attached' => [
                'library' => [
                    'user_provisioning/user_provisioning.admin',
                ]
            ],
        ];

        $current_status = $this->config->get('mo_user_provisioning_status');

        $form['header_top_style1'] = array(
            '#markup' => t('<div class="mo_user_provisioning_table_layout"><div class="mo_user_provisioning_container">'),
        );

        if ($current_status == 'VALIDATE_OTP') {
            $this->getValidateOTPForm($form);
        } elseif ($current_status == 'PLUGIN_CONFIGURATION') {
            $this->getProfileForm($form);
        } else {
            $this->getRegisterLoginForm($form);
        }
        moUserProvisioningUtilities::userProvisioningConfigGuide($form, $form_state);
        return $form;
    }

    private function getRegisterLoginForm(array &$form)
    {

        $form['mo_user_provisioning_register'] = array(
            '#type' => 'fieldset',
            '#title' => t('REGISTER/LOGIN WITH MINIORANGE'),
            '#attributes' => array('style' => 'padding:2% 2% 5%; margin-bottom:2%'),
            '#markup' => '<br><hr><br>',
        );

        $form['mo_user_provisioning_register']['markup_2'] = array(
            '#markup' => '<div class="mo_user_provisioning_background_note"><p><h3>Why Do I Need To Register?</h3></p>
            <b> You will be needing a miniOrange account to upgrade to the Premium version of the miniOrange User Provisioning module.</b><p>
             If you face any problem during registration, you can create an account by clicking <a href="https://www.miniorange.com/businessfreetrial" target="_blank">here</a>.<br>
             We do not store any information except the email that you will use to register with us.</p></div><br>',
        );

        $form['mo_user_provisioning_register']['mo_user_prov_customer_setup_username'] = array(
            '#type' => 'textfield',
            '#title' => t('Email'),
            '#attributes' => array('style' => 'width:50%;', 'placeholder' => 'Enter your email'),
            '#required' => TRUE,
        );

        $form['mo_user_provisioning_register']['mo_user_prov_customer_setup_phone'] = array(
            '#type' => 'textfield',
            '#title' => t('Phone'),
            '#attributes' => array('style' => 'width:50%;', 'placeholder' => 'Enter your phone number'),
            '#description' => '<b>NOTE:</b> We will only call if you need support.'
        );

        $form['mo_user_provisioning_register']['mo_user_prov_customer_setup_password'] = array(
            '#type' => 'password_confirm',
            '#required' => TRUE,
        );

        $form['mo_user_provisioning_register']['mo_user_prov_customer_setup_button'] = array(
            '#type' => 'submit',
            '#value' => t('Submit'),
            '#button_type' => 'primary',
        );

        $form['markup_closing_div'] = array(
            '#markup' => '</div>'
        );
    }

    private function getValidateOTPForm(array &$form)
    {
        $form['mo_user_provision_add_css'] = [
            '#attached' => [
                'library' => [
                    'user_provisioning/user_provisioning.admin',
                ]
            ],
        ];
        $form['mo_user_prov_otp_validation'] = array(
            '#type' => 'fieldset',
            '#title' => t('OTP VALIDATION'),
            '#attributes' => array('style' => 'padding:2% 2% 5%; margin-bottom:2%'),
            '#markup' => '<br><hr><br>',
        );

        $form['mo_user_prov_otp_validation']['mo_user_prov_customer_otp_token'] = array(
            '#type' => 'textfield',
            '#title' => t('OTP'),
            '#attributes' => array('style' => 'width:30%;'),
        );

        $form['mo_user_prov_otp_validation']['mo_btn_brk'] = array('#markup' => '<br><br>');

        $form['mo_user_prov_otp_validation']['mo_user_prov_customer_validate_otp_button'] = array(
            '#type' => 'submit',
            '#value' => t('Validate OTP'),
            '#submit' => array('::miniorangeUserProvisioningValidateOtp'),
        );

        $form['mo_user_prov_otp_validation']['mo_user_prov_customer_setup_resend_otp'] = array(
            '#type' => 'submit',
            '#value' => t('Resend OTP'),
            '#submit' => array('::miniorangeUserProvisioningResendOtp'),
        );

        $form['mo_user_prov_otp_validation']['mo_user_prov_customer_setup_back'] = array(
            '#type' => 'submit',
            '#value' => t('Back'),
            '#submit' => array('::miniorangeUserProvisioningBack'),
        );

        $form['markup_closing_div'] = array(
            '#markup' => '</div>'
        );
    }

    private function getProfileForm(array &$form)
    {
        $form['mo_user_provision_add_css'] = [
            '#attached' => [
                'library' => [
                    'user_provisioning/user_provisioning.admin',
                ]
            ],
        ];
        $form['mo_user_prov_profile'] = array(
            '#type' => 'fieldset',
            '#title' => t('PROFILE'),
            '#attributes' => array('style' => 'padding:2% 2% 5%; margin-bottom:2%'),
            '#markup' => '<br><hr><br>',
        );

        $form['mo_user_prov_profile']['mo_message_welcome'] = array(
            '#markup' => '<div class="mo_user_provisioning_welcome_message">Thank you for registering with miniOrange',
        );

        $form['mo_user_prov_profile']['mo_user_profile'] = array(
            '#markup' => '</div><br><br><h4>Your Profile: </h4>'
        );

        $options = array(
            ['Email', $this->config->get('mo_user_provisioning_customer_email')],
            ['Customer ID', $this->config->get('mo_user_provisioning_customer_id')],
            ['Token Key', $this->config->get('mo_user_provisioning_customer_token')],
            ['API Key', $this->config->get('mo_user_provisioning_customer_api_key')],
            ['PHP Version', phpversion()],
            ['Drupal Version', Drupal::VERSION],
        );

        $form['mo_user_prov_profile']['fieldset']['customerinfo'] = array(
            '#theme' => 'table',
            '#header' => ['ATTRIBUTE', 'VALUE'],
            '#rows' => $options,
        );

        $form['mo_user_prov_profile']['mo_firebase_support_div_cust'] = array(
            '#markup' => '<br><br><br><br>'
        );

        $form['markup_closing_div'] = array(
            '#markup' => '</div>'
        );
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $username = $form_values['mo_user_prov_customer_setup_username'];
        $phone = $form_values['mo_user_prov_customer_setup_phone'];
        $password = $form_values['mo_user_prov_customer_setup_password'];

        if (empty($username) || empty($password)) {
            $this->messenger()->addError(t('The <b><u>Email Address</u></b> and <b><u>Password</u></b> fields are mandatory.'));
            return;
        }

        $this->config_factory
            ->set("mo_user_provisioning_customer_email", $username)
            ->set("mo_user_provisioning_customer_phone", $phone)
            ->set("mo_user_provisioning_customer_password", $password)
            ->save();

        $customer = new moUserProvisioningCustomer($username, $phone, $password);
        $check_customer_response = json_decode($customer->checkCustomer());

        if ($check_customer_response->status === moUserProvisioningConstants::TRANSACTION_LIMIT_EXCEEDED) {
            $this->messenger()->addError(self::showErrorMessage());
            return;
        } elseif ($check_customer_response->status === moUserProvisioningConstants::API_CALL_FAILED) {
            $this->messenger()->addError($this->t($check_customer_response->message));
            return;
        } elseif ($check_customer_response->status === moUserProvisioningConstants::SUCCESS) {
            // Account exists. login the customer.
            $customer_keys_response = json_decode($customer->getCustomerKeys());
            self::customerAccountFound($customer_keys_response, $username, $phone);
        } elseif ($check_customer_response->status === moUserProvisioningConstants::CUSTOMER_NOT_FOUND) {
            $send_otp_response = json_decode($customer->sendOtp());
            if ($send_otp_response->status === moUserProvisioningConstants::TRANSACTION_LIMIT_EXCEEDED) {
                $this->messenger()->addError(self::showErrorMessage(' while sending OTP'));
                return;
            } elseif ($send_otp_response->status === moUserProvisioningConstants::SUCCESS) {
                self::otpSentSuccess($send_otp_response, $username);
            } else {
                $this->messenger()->addError($this->t($send_otp_response->message));
                return;
            }
        } else {
            $this->messenger()->addError(self::showErrorMessage(' while creating your account'));
            return;
        }
        return;
    }

    /**
     * @param $customer_keys_response
     * @param $username
     * @param $phone
     * @return void
     */
    public function customerAccountFound($customer_keys_response, $username, $phone)
    {
        if (json_last_error() == JSON_ERROR_NONE) {
            $this->config_factory
                ->set('mo_user_provisioning_customer_id', $customer_keys_response->id)
                ->set('mo_user_provisioning_customer_token', $customer_keys_response->token)
                ->set('mo_user_provisioning_customer_email', $username)
                ->set('mo_user_provisioning_customer_phone', $phone)
                ->set('mo_user_provisioning_customer_api_key', $customer_keys_response->apiKey)
                ->set('mo_user_provisioning_status', 'PLUGIN_CONFIGURATION')
                ->save();
            \Drupal::messenger()->addMessage(t('Successfully retrieved your account.'));
        }
    }

    /**
     * @param $create_customer_response
     * @return void
     */
    public function customerAccountCreated($create_customer_response)
    {
        $this->config_factory
            ->set('mo_user_provisioning_status', 'PLUGIN_CONFIGURATION')
            ->set('mo_user_provisioning_customer_token', $create_customer_response->token)
            ->set('mo_user_provisioning_customer_id', $create_customer_response->id)
            ->set('mo_user_provisioning_customer_api_key', $create_customer_response->apiKey)
            ->save();
        $this->messenger()->addStatus(t('Customer account created.'));
    }

    /**
     * @param $additional_param
     * @return Drupal\Core\StringTranslation\TranslatableMarkup
     */
    public function showErrorMessage($additional_param = null)
    {
        return t('An error has been occurred' . $additional_param . '. Please try after some time or <a href="mailto:drupalsupport@xecurify.com"><i>contact us</i></a>.');
    }

    /**
     * @param $send_otp_response
     * @param $username
     * @return void
     */
    public function otpSentSuccess($send_otp_response, $username)
    {
        $this->config_factory
            ->set("mo_user_provisioning_status", 'VALIDATE_OTP')
            ->set("mo_user_provisioning_transaction_id", $send_otp_response->txId)
            ->save();
        $this->messenger()->addStatus(t('Verify email address by entering the passcode sent to @username', [
            '@username' => $username
        ]));
    }

    /**
     * @return void
     */
    public function miniorangeUserProvisioningBack()
    {
        $this->config_factory
            ->clear("mo_user_provisioning_status")
            ->clear('mo_user_provisioning_customer_email')
            ->clear('mo_user_provisioning_customer_phone')
            ->clear('mo_user_provisioning_customer_password')
            ->clear('mo_user_provisioning_transaction_id')
            ->save();
        $this->messenger()->addStatus(t('Register/Login with your miniOrange Account'));
    }

    /**
     * @return void
     */
    public function miniorangeUserProvisioningResendOtp()
    {
        $this->config_factory->clear('mo_user_provisioning_transaction_id')->save();
        $username = $this->config->get('mo_user_provisioning_customer_email');
        $phone = $this->config->get('mo_user_provisioning_customer_phone');

        $customer = new moUserProvisioningCustomer($username, $phone);
        $send_otp_response = json_decode($customer->sendOtp());

        if ($send_otp_response->status == 'SUCCESS') {
            $this->config_factory->set('mo_user_provisioning_transaction_id', $send_otp_response->txId)->save();
            $this->messenger()->addStatus(t('Verify email address by entering the passcode sent to @username', array('@username' => $username)));
        } else {
            $this->messenger()->addError(t('An error has been occurred. Please try after some time'));
        }
    }

    /**
     * @param $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function miniorangeUserProvisioningValidateOtp(&$form, FormStateInterface $form_state)
    {
        $otpToken = $form_state->getValue('mo_user_prov_customer_otp_token');
        if (trim($otpToken) === "") {
            $this->messenger()->addError(t('OTP field is required.'));
            return;
        }

        $username = $this->config->get('mo_user_provisioning_customer_email');
        $phone = $this->config->get('mo_user_provisioning_customer_phone');
        $txId = $this->config->get('mo_user_provisioning_transaction_id');
        $password = $this->config->get('mo_user_provisioning_customer_password');

        $miniorange_user_prov_customer = new moUserProvisioningCustomer($username, $phone, $password, $otpToken);
        $validate_otp_response = json_decode($miniorange_user_prov_customer->validateOtp($txId));

        if ($validate_otp_response->status === moUserProvisioningConstants::SUCCESS) {
            $this->config_factory->clear('mo_user_provisioning_transaction_id')->save();
            $create_customer_response = json_decode($miniorange_user_prov_customer->createCustomer());
            if ($create_customer_response->status === 'SUCCESS') {
                self::customerAccountCreated($create_customer_response);
            } else if (trim($create_customer_response->status) == moUserProvisioningConstants::TEMP_EMAIL) {
                $this->messenger()->addMessage(t('There was an error creating an account for you.<br> You may have entered an invalid Email-Id
                <strong>(We discourage the use of disposable emails) </strong>
                <br>Please try again with a valid email.'), 'error');
            } else {
                $this->messenger()->addMessage(t('There was an error while creating customer. Please try after some time.'), 'error');
            }
        } else {
            $this->messenger()->addError(t('Invalid OTP'));
        }
        return;
    }

}
