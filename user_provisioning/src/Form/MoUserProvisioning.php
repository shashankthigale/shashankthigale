<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\user_provisioning\Helpers\moProviderSpecificProvisioning;
use Drupal\user_provisioning\Helpers\moSCIMClient;
use Drupal\user_provisioning\Helpers\moUserProvisioningLogger;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\moUserProvisioningOperationsHandler;
use Drupal\user_provisioning\moUserProvisioningUtilities;
use Drupal\user_provisioning\ProviderSpecific\EntityHandler\moUserProvisioningUserHandler;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MoUserProvisioning extends FormBase
{

    protected $messenger;

    private $base_url;

    private ImmutableConfig $config;

    private Config $config_factory;

    private LoggerInterface $logger;

    private moUserProvisioningLogger $mo_logger;

    private Request $request;

    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->config = Drupal::config('user_provisioning.settings');
        $this->config_factory = Drupal::configFactory()
            ->getEditable('user_provisioning.settings');
        $this->logger = Drupal::logger('user_provisioning');
        $this->messenger = Drupal::messenger();
        $this->mo_logger = new moUserProvisioningLogger();
        $this->request = \Drupal::request();
    }

    /**
     * @inheritDoc
     */
    public function getFormId(): string
    {
        return "mo_user_provisioning";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $form['mo_user_provisioning_add_css'] = [
            '#attached' => [
                'library' => [
                    'user_provisioning/user_provisioning.admin',
                    'user_provisioning/user_provisioning.mo_search_field',
                ],
            ],
        ];

        //below section is for creating the horizontal tab.
        $mo_table_content = [
            [
                Markup::create('<a href="' . $this->base_url . moUserProvisioningConstants::USER_PROVISIONING . '"> <strong>SCIM CLIENT<strong> </a>'),
                Markup::create('<a href="' . $this->base_url . moUserProvisioningConstants::USER_PROVISIONING . '?tab_name=' . moUserProvisioningConstants::SCIM_SERVER_TAB_NAME . '"> <strong>SCIM SERVER<strong> </a>'),
                Markup::create('<a href="' . $this->base_url . moUserProvisioningConstants::USER_PROVISIONING . '?tab_name=' . moUserProvisioningConstants::PROVIDER_SPECIFIC_PROVISIONING_TAB_NAME . '"> <strong>PROVIDER SPECIFIC PROVISIONING<strong> </a>'),
            ],
        ];

        $form['mo_user_provisioning_navbar'] = [
            '#markup' => t('<div class="mo_user_provisioning_table_layout">'),
        ];

        $form['mo_user_provisioning_table'] = [
            '#type' => 'table',
            '#responsive' => TRUE,
            '#rows' => $mo_table_content,
            '#attributes' => [
                'style' => 'border-collapse: separate;',
                'id' => 'user_provisioning_headers',
                'class' => ['border_fill'],
            ],
        ];

        $form['mo_user_provisioning_navbar_1'] = [
            '#markup' => t('<div class="mo_user_provisioning_container_user_provisioning_tab">'),
        ];

        //horizontal tab section ends here

        $tab_name = $this->getTabName();

        if ($tab_name == moUserProvisioningConstants::SCIM_SERVER_TAB_NAME) {
            $this->getSCIMServerForm($form);
            moUserProvisioningUtilities::userProvisioningConfigGuide($form, $form_state);
        } elseif ($tab_name == moUserProvisioningConstants::PROVIDER_SPECIFIC_PROVISIONING_TAB_NAME) {
            $this->getProviderSpecificForm($form);
            moUserProvisioningUtilities::userProvisioningConfigGuide($form, $form_state);
        } else {
            $this->getSCIMClientForm($form);
            $application_selected = $this->config->get('mo_user_provisioning_app_name');
            if (empty($this->request->get('app_name')) && empty($application_selected)) {
                moUserProvisioningUtilities::userProvisioningConfigGuide($form, $form_state);
            }
        }
        return $form;
    }

    /**
     * Fetches and return the name of the tab from the get parameter
     */
    private function getTabName()
    {
        return Drupal::request()->get('tab_name');
    }

    /**
     * Generates and returns the form for the SCIM SERVER configuration.
     *
     * @param array $form
     *
     * @return void
     */
    private function getSCIMServerForm(array &$form)
    {
        $url = $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS;
        $mo_premium_tag = '<a href= "' . $url . '" >[PREMIUM]</a>';

        $mo_table_content = [
            [
                'SCIM Base URL',
                $this->t('This feature is available in the <a href="' . $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS . '">Premium</a> version of the module.'),
            ],
            [
                'SCIM Bearer Token',
                $this->t('This feature is available in the <a href="' . $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS . '">Premium</a> version of the module.'),
            ],
        ];

        $form['mo_user_provisioning_scim_server_configuration'] = [
            '#type' => 'fieldset',
            '#title' => t('CONFIGURE DRUPAL AS A SCIM SERVER: ') . $mo_premium_tag . '<hr>',
            '#attributes' => ['style' => 'padding:2% 2% 4%; margin-bottom:2%'],
        ];

        $form['mo_user_provisioning_scim_server_configuration']['mo_user_provisioning_scim_server_meatadata'] = [
            '#type' => 'table',
            '#header' => ['ATTRIBUTE', 'VALUE'],
            '#rows' => $mo_table_content,
            '#empty' => t('Something is not right. Please run the update script or contact us at <a href="mailto:drupalsupport@xecurify.com">drupalsupport@xecurify.com</a>'),
            '#responsive' => TRUE,
            '#sticky' => TRUE,
            '#size' => 2,
            '#prefix' => '<br>',
        ];

        $form['mo_user_provisioning_scim_server_configuration']['mo_user_provisioning_scim_server_generate_new_token'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => t('Generate new Token'),
            '#disabled' => TRUE,
            '#prefix' => '<br/>',
            '#suffix' => '<br/>',
        ];

        $form['mo_user_provisioning_scim_server_configuration']['mo_user_provisioning_scim_server_description'] = [
            '#markup' => t('</br></br>
                <div class="mo_user_provisioning_tab_sub_heading">This tab is capable of doing following SCIM Operations <strong>' . $mo_premium_tag . '</strong></div><hr>
                <br><div class="mo_user_provisioning_background_note">
                    <strong>Create:</strong>
                    <ol>It will create users using usernames and other attributes of IDP.<br/>
                    <strong>Note: </strong>If Username field is blank, it will copy email as a username, as Drupal does not accept blank Username.</ol>
                    <strong>Update:</strong>
                    <ol>It will update the user fields, all attributes/fields except email and username.</ol>
                    <strong>Delete:</strong>
                    <ol>Once a user is deleted from IDP, it would delete a user from Drupal User list as well.</ol>
                    <strong>Deactivate:</strong>
                    <ol>Once a user is deleted from IDP, it would deactivate a user from the Drupal user list.</ol>
                </div>
                </div>'),
        ];

    }

    /**
     * Generates and returns the form for the Provider Specific Provisioning
     * configuration.
     *
     * @param array $form
     *
     * @return void
     */
    private function getProviderSpecificForm(array &$form)
    {
        $form['mo_user_provisioning_text_search'] = [
            '#type' => 'textfield',
            '#prefix' => '<div class="mo_user_provisioning_search_provider"><br/>',
            '#title' => $this->t('Search Provider/Application'),
            '#placeholder' => $this->t('Search your Provider'),
            '#attributes' => [
                'id' => 'mo_text_search',
                'onkeyup' => 'searchApp()',
            ],
            '#suffix' => '</div><br>',
        ];

        $provider_specific_provisioning = new moProviderSpecificProvisioning();
        $api_providers = $provider_specific_provisioning->providerList();

        $form['mo_user_provisioning_application_list'] = [
            '#prefix' => '<ul id="mo_search_ul" class="mo_user_provisioning_wrap mo_user_provisioning_flex_container"><li class="mo_user_provisioning_flex_item disabled">',
            '#markup' => implode('</li><li class="mo_user_provisioning_flex_item disabled">', $api_providers),
            '#suffix' => '</li></ul> ',
        ];

        $form['markup_closing_div'] = array(
            '#markup' => '</div>'
        );

    }

    /**
     * Generates and returns the form for the SCIM CLIENT configuration.
     *
     * @param array $form
     *
     * @return void
     */
    private function getSCIMClientForm(array &$form)
    {

        $application_selected = $this->config->get('mo_user_provisioning_app_name');
        if (empty($this->request->get('app_name')) && empty($application_selected)) {
            $form['mo_user_provisioning_text_search'] = [
                '#type' => 'textfield',
                '#prefix' => '<div class="mo_user_provisioning_search_provider"><br/>',
                '#title' => $this->t('Search Provider/Application'),
                '#placeholder' => $this->t('Search your Provider'),
                '#attributes' => [
                    'id' => 'mo_text_search',
                    'onkeyup' => 'searchApp()',
                ],
                '#suffix' => '</div><br>',
            ];

            $provider_specific_provisioning = new moSCIMClient();
            $api_providers = $provider_specific_provisioning->providerList();
            $custom_application = $provider_specific_provisioning->providerList('custom');

            $form['mo_user_provisioning_application_list'] = [
                '#prefix' => '<ul id="mo_search_ul" class="mo_user_provisioning_wrap mo_user_provisioning_flex_container"><li class="mo_user_provisioning_flex_item">',
                '#markup' => implode('</li><li class="mo_user_provisioning_flex_item">', $api_providers),
                '#suffix' => '</li></ul> ',
            ];

            $form['mo_user_provisioning_custom_application'] = [
                '#prefix' => '<ul class="mo_user_provisioning_wrap mo_user_provisioning_flex_container"><li class="mo_user_provisioning_flex_item">',
                '#markup' => implode('</li><li class="mo_user_provisioning_flex_item">', $custom_application),
                '#suffix' => '</li></ul> ',
            ];
            $form['markup_closing_div'] = array(
                '#markup' => '</div>'
            );
            return;
        }

        $url = $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS;
        $mo_premium_tag = '<a href= "' . $url . '" >[PREMIUM]</a>';

        $form['mo_user_provisioning_config_fieldset'] = [
            '#type' => 'details',
            '#title' => t('CONFIGURE DRUPAL AS A SCIM CLIENT: &nbsp;<a href="' . $this->base_url . moUserProvisioningConstants::OVERVIEW . '">[Know more]</a> &nbsp <a class="button button--primary" target="_blank" href="' . self::getSetupGuide() . '">Setup Guide</a>'),
            '#open' => empty($this->config->get('mo_user_provisioning_scim_client_enable_api_integration')) || empty($this->config->get('mo_user_provisioning_scim_server_base_url')),
        ];

        $form['mo_user_provisioning_config_fieldset']['mo_user_provisioning_scim_client_enable_api_integration'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Enable SCIM CLIENT API integration'),
            '#default_value' => $this->config->get('mo_user_provisioning_scim_client_enable_api_integration'),
            '#description' => '<strong>Note:-</strong> ' . $this->t('Enable this checkbox to update below configuration and activate the provisioning.'),
            '#prefix' => '<br/>',
            '#suffix' => '<br/>',
        ];

        $form['mo_user_provisioning_config_fieldset']['mo_user_provisioning_scim_server_base_url'] = [
            '#type' => 'url',
            '#title' => $this->t('SCIM 2.0 Base Url') . '&emsp;&emsp;&ensp;',
            '#default_value' => $this->config->get('mo_user_provisioning_scim_server_base_url'),
            '#wrapper_attributes' => [
                'class' => 'mo_user_provisioning_inline_wrapper',
            ],
            '#attributes' => ['placeholder' => $this->t('SCIM endpoint of the application')],
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
        ];

        $form['mo_user_provisioning_config_fieldset']['mo_user_provisioning_scim_server_bearer_token'] = [
            '#type' => 'textfield',
            '#title' => $this->t('SCIM Bearer Token') . '&emsp;&emsp;',
            '#maxlength' => 1024,
            '#default_value' => $this->config->get('mo_user_provisioning_scim_server_bearer_token'),
            '#wrapper_attributes' => [
                'class' => 'mo_user_provisioning_inline_wrapper',
            ],
            '#attributes' => ['placeholder' => $this->t('Bearer Token')],
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
            '#suffix' => '<br/>',
        ];

        $form['mo_user_provisioning_config_fieldset']['mo_user_provisioning_configuration_test'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Save and Test Credentials'),
            '#submit' => ['::testAPICredentials'],
            '#prefix' => '<br/>',
        ];

        $form['mo_user_provisioning_config_fieldset']['mo_user_provisioning_configuration_reset'] = [
            '#type' => 'submit',
            '#button_type' => 'danger',
            '#value' => $this->t('Reset Configuration'),
            '#submit' => ['::resetConfigurations'],
        ];

        $form['provisioning_operations'] = array(
            '#type' => 'details',
            '#title' => $this->t('Provisioning operations:'),
            '#open' => true,
        );

        $form['provisioning_operations']['mo_user_provisioning_create_user'] = [
            '#type' => 'checkbox',
            '#title' => '<strong>' . $this->t('Create User ') . '</strong>',
            '#default_value' => $this->config->get('mo_user_provisioning_create_user'),
            '#description' => '<strong>' . $this->t('Note:-') . '</strong>' . $this->t('Allows creation of users at the configured application. It includes creation of new users and provisioning of existing users.'),
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
        ];

        $form['provisioning_operations']['mo_user_provisioning_update_user'] = [
            '#type' => 'checkbox',
            '#title' => '<strong>' . $this->t('Update User ') . '</strong>' . $mo_premium_tag,
            '#disabled' => TRUE,
            '#default_value' => $this->config->get('mo_user_provisioning_update_user'),
            '#description' => '<strong>' . $this->t('Note:-') . '</strong>' . $this->t('Allows updating users\' field at the configured application. Future attribute changes made to Drupal user profile will automatically override the corresponding attribute value at the configured application.'),
        ];

        $form['provisioning_operations']['mo_user_provisioning_delete_user'] = [
            '#type' => 'checkbox',
            '#title' => '<strong>' . $this->t('Delete User ') . '</strong>' . $mo_premium_tag,
            '#disabled' => TRUE,
            '#default_value' => $this->config->get('mo_user_provisioning_delete_user'),
            '#description' => '<strong>' . $this->t('Note:-') . '</strong>' . $this->t('Allows deletion of users at the configured application if the user is deleted from the Drupal.'),
        ];

        $form['provisioning_operations']['save_operations'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Save Operations'),
            '#submit' => ['::saveOperations'],
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
        ];

        $form['additional_configuration'] = array(
            '#type' => 'details',
            '#title' => $this->t('Additional configuration:'),
            '#open' => true,
        );

        $form['additional_configuration']['event_based_provisioning'] = [
            '#type' => 'checkbox',
            '#title' => '<strong>' . $this->t('Enable Event Based provisioning ') . '</strong>',
            '#default_value' => $this->config->get('event_based_provisioning'),
            '#description' => '<strong>Note:- </strong>' . $this->t('Enable this checkbox to perform provisioning when CRUD and related events are performed in Drupal'),
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
        ];

        $form['additional_configuration']['scheduler_based_provisioning'] = [
            '#type' => 'checkbox',
            '#title' => '<strong>' . $this->t('Enable Scheduler Based provisioning ') . '</strong>' . $mo_premium_tag,
            '#description' => '<strong>Note:- </strong>' . $this->t('Enable this checkbox to perform provisioning periodically i.e. sync and provisioning operations will keep on triggering themselves after regular intervals of time'),
            '#disabled' => TRUE,
        ];

        $form['additional_configuration']['mo_user_provisioning_role_provisioning'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('<strong>Enable Role Provisioning </strong>') . $mo_premium_tag,
            '#disabled' => TRUE,
            '#default_value' => $this->config->get('mo_user_provisioning_role_provisioning'),
            '#description' => '<strong>Note:- </strong>' . $this->t('Enable this checkbox to provision/sync your Drupal users based on their roles.'),
        ];

        $form['additional_configuration']['save_additional_configuration'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Save Configuration'),
            '#submit' => ['::saveAdditionalConfiguration'],
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_scim_client_enable_api_integration"]' => ['checked' => FALSE],],],
        ];


        $form['mo_user_provisioning_scim_client_mapping'] = [
            '#type' => 'details',
            '#title' => $this->t('Mapping'),
            '#open' => TRUE,
        ];

        $form['mo_user_provisioning_scim_client_mapping']['mo_user_provisioning_scim_client_attribute_mapping_header'] = [
            '#markup' => '<br/><strong class="mo_user_provisioning_scim_header">Attribute Mapping  </strong><hr><br/>',
        ];

        $fields = moUserProvisioningUtilities::customUserFields();
        $fields['name'] = 'Username'; //this is updated to avoid the confusion between name and username

        if ($this->request->get('app_name') == moUserProvisioningConstants::AWS_SSO || $application_selected == moUserProvisioningConstants::AWS_SSO) {
            $form['mo_user_provisioning_scim_client_mapping']['mo_user_provisioning_scim_client_attribute_mapping_aws_header'] = [
                '#markup' => '<h5>AWS Mapping </h5><div class="mo_user_provisioning_background_note"><strong>Note: </strong> This section is mandatory if you want to create user in AWS SSO. You have to select the machine name of the First and Last name respectively.</div>',
            ];

            $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_aws_mapping_table'] = array(
                '#type' => 'table',
                '#responsive' => TRUE,
                '#attributes' => ['style' => 'border-collapse: separate;'],
            );

            $row = $this->moAwsAttributes($fields);
            $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_aws_mapping_table']['attributes'] = $row;

            $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_aws_mapping_save'] = [
                '#type' => 'submit',
                '#button_type' => 'primary',
                '#value' => $this->t('Save Mapping'),
                '#submit' => ['::saveAwsMapping'],
                '#suffix' => '<br><br>'
            ];
        }

        $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_attribute_mapping_table'] = array(
            '#type' => 'table',
            '#responsive' => TRUE,
            '#attributes' => ['style' => 'border-collapse: separate;'],
            '#prefix' => '<br><br><strong class="mo_user_provisioning_scim_header">Add Attributes </Strong><a onclick="addButtonClick()" class="button">+</a>' . $mo_premium_tag . ' <br><br><div class="mo_user_provisioning_background_note"><strong>Note: </strong>Under below section, you can add the configuration to send the attributes from Drupal site to your
                            configured SCIM SERVER application. </div>',
        );

        $data = ['name' => 'username', 'mail' => 'email'];
        $mapping_headings = ['name' => 'Username Attribute', 'mail' => 'Email Attribute'];

        foreach ($data as $key => $value) {
            $row = $this->moAddAttributes($key, $value, $fields, $mapping_headings);
            $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_attribute_mapping_table'][$key] = $row;
        }

        $form['mo_user_provisioning_scim_client_mapping']['miniorange_scim_client_div_end'] = array(
            '#markup' => '</div>',
        );

        self::provisionOnDemandForm($form);
    }

    public function getSetupGuide()
    {
        $application_name = $this->request->get('app_name');
        $setup_guides = array(
            moUserProvisioningConstants::AWS_SSO => moUserProvisioningConstants::AWS_SSO_GUIDE,
            moUserProvisioningConstants::WORDPRESS => moUserProvisioningConstants::WORDPRESS_GUIDE,
            moUserProvisioningConstants::Drupal => moUserProvisioningConstants::DRUPAL_GUIDE,
            moUserProvisioningConstants::JOOMLA => moUserProvisioningConstants::JOOMLA_GUIDE,
            moUserProvisioningConstants::CUSTOM_APP => moUserProvisioningConstants::CUSTOM_APP_GUIDE,
        );
        return $setup_guides[$application_name];
    }

    /**
     * Saves the configuration of the additional configuration section in the SCIM Client tab of the module
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function saveAdditionalConfiguration(array &$form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $event_based_provisioning = $form_values['event_based_provisioning'];
        $this->config_factory->set('event_based_provisioning', $event_based_provisioning)->save();
        $this->messenger->addMessage('Additional configuration has been saved successfully.');
        $application_name = $this->request->get('app_name');
        $this->redirect('user_provisioning.user_provisioning', ['app_name' => $application_name,])->send();
    }

    /**
     * Saves the configuration of the Provisioning Operations section in the SCIM Client tab of the module
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function saveOperations(array &$form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $create_user = $form_values['mo_user_provisioning_create_user'];
        $this->config_factory->set('mo_user_provisioning_create_user', $create_user)->save();
        $this->messenger->addMessage('Provisioning operation configuration has been saved successfully.');
        $application_name = $this->request->get('app_name');
        $this->redirect('user_provisioning.user_provisioning', ['app_name' => $application_name,])->send();
    }

    /**
     * Provision resource on the fly.
     *
     * @param array $form
     *
     * @return void
     */
    public function provisionOnDemandForm(array &$form)
    {
        $url = $this->base_url . moUserProvisioningConstants::UPGRADE_PLANS;
        $mo_premium_tag = '<a href= "' . $url . '" >[PREMIUM]</a>';

        $form['mo_user_provisioning_provision_container'] = [
            '#markup' => '<div class="mo_user_provisioning_right_table_layout mo_user_provisioning_right_container">',
        ];

        $form['mo_user_provisioning_provision_on_demand'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('CHOOSE WHEN TO TRIGGER USER SYNC & PROVISIONING'),
        ];

        $form['mo_user_provisioning_provision_on_demand']['mo_user_provisioning_on_demand'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('On-demand/ Manual Provisioning '),
            '#description' => $this->t('This will allow you to manually sync any Drupal user in your SCIM Server application. <br>You can select which of the CRUD operations you wish to perform from under the Enable Provisioning Features section'),
            '#default_value' => TRUE,
            '#prefix' => '<br>'
        ];

        $form['mo_user_provisioning_provision_on_demand']['mo_user_provisioning_add_user_or_resource_field'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Enter the Drupal Username'),
            '#autocomplete_route_name' => 'user_provisioning.autocomplete',
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_on_demand"]' => ['checked' => FALSE],],],
            '#suffix' => '<br/>'
        ];

        $form['mo_user_provisioning_provision_on_demand']['mo_user_provisioning_provision_on_demand_submit'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#name' => 'UserProvision',
            '#value' => $this->t('Provision'),
            '#submit' => ['::performProvisionOnDemand'],
            '#states' => ['disabled' => [':input[name = "mo_user_provisioning_on_demand"]' => ['checked' => FALSE],],],
        ];
    }

    /**
     * @inheritDoc
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $triggering_element = $form_state->getTriggeringElement();
        if ($triggering_element['#name'] == 'UserProvision') {
            $entity_name = trim($form_state->getValues()['mo_user_provisioning_add_user_or_resource_field']);
            if (empty($entity_name)) {
                $form_state->setErrorByName('mo_user_provisioning_add_user_or_resource_field', $this->t('Enter a valid Drupal Username.'));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $tab_name = $this->getTabName();

        if ($tab_name == moUserProvisioningConstants::SCIM_SERVER_TAB_NAME) {

        } elseif ($tab_name == moUserProvisioningConstants::PROVIDER_SPECIFIC_PROVISIONING_TAB_NAME) {

        } else {
            $this->submitSCIMClientForm($form, $form_state);
        }
    }

    /**
     * Generates and returns the form for SCIM CLIENT configuration.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     *
     * @return void
     */
    private function submitSCIMClientForm(array &$form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();
        $enable_scim_client_api_integration = $form_values['mo_user_provisioning_scim_client_enable_api_integration'];
        $this->config_factory->set('mo_user_provisioning_scim_client_enable_api_integration', $enable_scim_client_api_integration)
            ->save();

        $application_name = $this->request->get('app_name');
        if (empty($application_name)) {
            $application_name = $this->config->get('mo_user_provisioning_app_name');
        }
        $scim_server_base_url = trim($form_values['mo_user_provisioning_scim_server_base_url']);
        $scim_server_bearer_token = trim($form_values['mo_user_provisioning_scim_server_bearer_token']);

        $show_success_message = FALSE;
        if ($scim_server_base_url != $this->config->get('mo_user_provisioning_scim_server_base_url')
            || $scim_server_bearer_token != $this->config->get('mo_user_provisioning_scim_server_bearer_token')
        ) {
            $show_success_message = TRUE;
        }

        if ($enable_scim_client_api_integration == TRUE) {
            $this->config_factory
                ->set('mo_user_provisioning_scim_server_base_url', $scim_server_base_url)
                ->set('mo_user_provisioning_scim_server_bearer_token', $scim_server_bearer_token)
                ->set('mo_user_provisioning_app_name', $application_name)
                ->save();
        }

        if ($show_success_message) {
            $this->messenger->addMessage($this->t('SCIM CLIENT configuration saved successfully.'));
        }
    }

    /**
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function testAPICredentials(array &$form, FormStateInterface $form_state)
    {
        $this->submitSCIMClientForm($form, $form_state);
        $user_provisioner = new moUserProvisioningUserHandler(User::load(Drupal::currentUser()->id()));
        [$status_code, $content] = $user_provisioner->searchResource();

        if (!is_null($content)) {
            $body = json_decode($content, TRUE);
            if ($status_code == 409 || !is_null($body)) {
                $this->logger->info('<b> ' . __FUNCTION__ . ':</b> ' . '<pre><code>' . print_r($content, TRUE) . '</code></pre>');
                $this->messenger->addMessage('Authorization successful. You can try Provision On-demand at the right section of this page.');
            } else {
                $this->logger->error('<b> ' . __FUNCTION__ . ':</b> ' . '<pre><code>' . print_r($content, TRUE) . '</code></pre>');
                $this->messenger->addError('Invalid Authorization.');
            }
        } else {
            $this->messenger->addError($this->t('Either base url or bearer token is invalid. Check' . ' <a href="' . $this->base_url . moUserProvisioningConstants::DRUPAL_LOGS_PATH . '" target="_blank">Drupal logs</a> ' . ' for more details.'));
        }
    }

    /**
     * Provision the User/role on demand.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     *
     * @return void
     */
    public function performProvisionOnDemand(array &$form, FormStateInterface $form_state)
    {
        //@TODO Provision user or role on-demand

        if ($this->config->get('mo_user_provisioning_scim_client_enable_api_integration') != true) {
            $this->messenger->addError($this->t('Enable the SCIM CLIENT API integration to perform provisioning.'));
            return;
        }

        $entity_name = trim($form_state->getValues()['mo_user_provisioning_add_user_or_resource_field']);

        $this->mo_logger->addLog('Provisioning on demand. Entered entity name is :' . $entity_name, __LINE__, __FUNCTION__, __FILE__);

        $entity = $this->getEntityToProvision($entity_name);

        if (is_null($entity)) {
            $this->messenger->addError('"' . $entity_name . '"' . $this->t(' can not be recognized as a valid User or Role. Enter a valid entity( User or Role) to provision.'));
            return;
        }

        //check if the selected application is AWS and configured mapping, and it will return error message if any issues. Otherwise, it will return false.
        $is_app_aws = $this->checkIfApplicationIsAws($entity, $entity_name);
        if ($is_app_aws) {
            $this->messenger->addError(t($is_app_aws));
            return;
        }

        $mo_entity_handler = new moUserProvisioningOperationsHandler($entity);
        try {
            $result = $mo_entity_handler->insert();
            if (is_null($result)) {
                $this->messenger->addError($this->t('An error occurred while provisioning the user, please refer to <a href="' . $this->base_url . moUserProvisioningConstants::DRUPAL_LOGS_PATH . '">drupal logs</a> for more information.'));
                return;
            }
            $this->messenger->addMessage($this->t($entity->label() . ' successfully created at the configured application.'));
        } catch (Exception $exception) {
            if ($exception->getCode() == moUserProvisioningConstants::STATUS_CONFLICT) {
                $this->messenger->addError($this->t('The application has returned 409 conflict i.e., the user already exists at the configured application.'));
            } else {
                $this->messenger->addError($exception->getMessage());
            }
            // TODO PUT operation will be called following this event to update the user fields
        }
    }

    /**
     * @param EntityInterface $entity
     * @param $entity_name
     * @return false|string|void
     */
    public function checkIfApplicationIsAws(EntityInterface $entity, $entity_name)
    {
        $application_name = $this->config->get('mo_user_provisioning_app_name');
        if ($application_name != moUserProvisioningConstants::AWS_SSO) {
            return false;
        }

        $first_name_attr = $this->config->get('mo_user_provisioning_scim_client_fname_attr');
        $last_name_attr = $this->config->get('mo_user_provisioning_scim_client_lname_attr');

        if (empty($first_name_attr) || empty($last_name_attr)) {
            return 'Please select both First and Last name attributes in the <strong>AWS Mapping</strong> under Attribute Mapping section.';
        } else if (empty($entity->get($first_name_attr)->value) || empty($entity->get($last_name_attr)->value)) {
            return 'It seems that ' . $entity_name . ' does not have First and Last name in their profile. Please make sure these fields are not empty for ' . $entity_name . '.';
        }
    }

    /**
     * Checks and return the User object or Role object. Returns null if not
     * found. (Checks for User and then Role)
     *
     * @param string $entity_name User or Role name
     *
     * @return EntityBase|EntityInterface|User|null
     */
    private function getEntityToProvision(string $entity_name)
    {
        $user = user_load_by_name($entity_name);
        if ($user != FALSE) {
            return User::load($user->id());
        }
        return NULL;
    }

    /**
     * Generates and returns the form for SCIM SERVER configuration.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     *
     * @return void
     */
    private function submitSCIMServerForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * Generates and returns the form for Provider Specific Provisioning
     * configuration.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     *
     * @return void
     */
    private function submitProviderSpecificForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * Adds UI for First and Last name attributes
     *
     * @param $options
     * @return array
     */
    public function moAwsAttributes($options): array
    {
        $row['mo_user_provisioning_scim_client_fname_attr'] = [
            '#type' => 'select',
            '#title' => t('First Name Attribute'),
            '#options' => $options,
            '#default_value' => $this->config->get('mo_user_provisioning_scim_client_fname_attr'),
        ];

        $row['mo_user_provisioning_scim_client_lname_attr'] = [
            '#type' => 'select',
            '#title' => t('Last Name Attribute'),
            '#options' => $options,
            '#default_value' => $this->config->get('mo_user_provisioning_scim_client_lname_attr'),
        ];

        return $row;
    }

    /**
     * Returns the Username and Email Attribute Mapping fields
     *
     * @param $key
     * @param $value
     * @param $fields
     * @param $mapping_headings
     * @return array
     */
    public function moAddAttributes($key, $value, $fields, $mapping_headings): array
    {
        $row['mo_user_provisioning_scim_client_' . $key] = [
            '#type' => 'select',
            '#title' => t($mapping_headings[$key]),
            '#options' => $fields,
            '#disabled' => TRUE,
            '#default_value' => $key,
        ];

        $row['mo_user_provisioning_scim_client_' . $value] = [
            '#type' => 'textfield',
            '#title' => t('Value'),
            '#disabled' => TRUE,
            '#default_value' => $value,
        ];

        return $row;
    }

    /**
     * Saves the extra mapping (First name and Last name) required for AWS SSO
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function saveAwsMapping(array &$form, FormStateInterface $form_state)
    {
        $form_values = $form_state->getValues();

        $fname_attr = $form_values['miniorange_scim_client_aws_mapping_table']['attributes']['mo_user_provisioning_scim_client_fname_attr'];
        $lname_attr = $form_values['miniorange_scim_client_aws_mapping_table']['attributes']['mo_user_provisioning_scim_client_lname_attr'];

        if (empty($fname_attr) || empty($lname_attr)) {
            $this->messenger()->addError(t('Please select both the First name as well as Last name attributes.'));
            return;
        }

        $this->config_factory
            ->set('mo_user_provisioning_scim_client_fname_attr', $fname_attr)
            ->set('mo_user_provisioning_scim_client_lname_attr', $lname_attr)
            ->save();

        $this->messenger->addStatus(t('Configuration saved successfully.'));
    }

    /**
     * Clears the configurations when clicked on Reset Configuration button
     *
     * @return void
     */
    public function resetConfigurations()
    {
        $this->config_factory
            ->clear('mo_user_provisioning_scim_client_enable_api_integration')
            ->clear('mo_user_provisioning_app_name')
            ->clear('mo_user_provisioning_scim_server_bearer_token')
            ->clear('mo_user_provisioning_scim_server_base_url')
            ->clear('mo_user_provisioning_scim_client_fname_attr')
            ->clear('mo_user_provisioning_scim_client_lname_attr')
            ->save();

        $response = new RedirectResponse(Url::fromRoute('user_provisioning.user_provisioning')->toString());
        $response->send();
    }
}
