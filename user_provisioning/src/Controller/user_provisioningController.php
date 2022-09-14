<?php

namespace Drupal\user_provisioning\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserStorageInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class user_provisioningController extends ControllerBase
{
    /**
     * The user storage.
     *
     * @var UserStorageInterface
     */
    protected $users;

    protected $roles;

    private $base_url;
    private ImmutableConfig $config;
    private Config $config_factory;
    private LoggerInterface $logger;
    protected $formBuilder;

    /**
     * scim_clientController constructor.
     * @throws InvalidPluginDefinitionException
     * @throws PluginNotFoundException
     */
    public function __construct(FormBuilder $formBuilder)
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->config = \Drupal::config('user_provisioning.settings');
        $this->config_factory = \Drupal::configFactory()->getEditable('user_provisioning.settings');
        $this->logger = \Drupal::logger('user_provisioning');

        // Loading all the users
        $this->users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple();
        unset($this->users[0]); //Un-setting the Anonymous user which has uid=0

        // Loading all the roles
        $this->roles = Role::loadMultiple();
        $this->formBuilder = $formBuilder;
    }

    /**
     * @throws InvalidPluginDefinitionException
     * @throws PluginNotFoundException
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get("form_builder")
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handleAutocomplete(Request $request): JsonResponse
    {
        $matches = [];

        //fetching the entered query string
        $typed_field_value = $request->query->get('q');

        //Loading all the users and preparing array for json response
        foreach ($this->users as $user) {
            if (stripos($user->label(), $typed_field_value) === 0) {
                $matches[] = ['value' => $user->label(), 'label' => Html::escape($user->label() . '(' . $user->getEntityType()->getLabel()->getUntranslatedString() . ')')];
            }
        }

        return new JsonResponse($matches);
    }

    /**
     * Export the user in json or csv file
     * @return RedirectResponse|void
     */
    public function exportUsers()
    {
        $users_export = $this->config->get('mo_user_provisioning_export_config');     // required attribute to export
        $fil_extension = $this->config->get('mo_user_provisioning_file_extension');    // file extension
        $this->config_factory->clear('mo_export_config')->save();

        $configuration_array = array();

        $header = array();
        if ($fil_extension == 'csv') {
            foreach ($users_export as $key => $value) {
                if ($value) {
                    $header[] = $key;
                }
            }
            $configuration_array[] = $header;
        }

        foreach ($this->users as $user) {
            $user_details = array();
            foreach ($users_export as $key => $val) {
                if ($val) {
                    $user_key_value = $user->get($key)->value;
                    $user_details[$key] = $user_key_value;
                }
            }
            if (!empty($user_details)) {
                $configuration_array[$user->getDisplayName()] = $user_details;
            }

        }

        // to unset the anonymous user
        unset($configuration_array["Anonymous"]);

        if ($fil_extension == 'json') {
            header("Content-Disposition: attachment; filename = mo_user_provisioning_export.json");
            echo(json_encode($configuration_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            exit;
        } else if ($fil_extension == 'csv') {
            $f = fopen('php://memory', 'w');
            // loop over the input array
            foreach ($configuration_array as $line) {
                // generate csv lines from the inner arrays
                fputcsv($f, $line, ';');
            }
            // reset the file pointer to the start of the file
            fseek($f, 0);
            // tell the browser it's going to be a csv file
            header('Content-Type: text/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename=mo_user_provisioning_export.csv');
            // make php send the generated csv lines to the browser
            fpassthru($f);
            exit;
        }

        return new RedirectResponse(Url::fromRoute('user_provisioning.advanced_settings')->toString());
    }

    // Ajax Response for Trial Request form

    /**
     * @return AjaxResponse
     */
    public function openTrialRequestForm()
    {
        $response = new AjaxResponse();
        $modal_form = $this->formBuilder->getForm('\Drupal\user_provisioning\Form\MoUserProvisioningRequestTrial');
        $response->addCommand(new OpenModalDialogCommand('Request 7-Days Full Feature Trial License', $modal_form, ['width' => '40%']));
        return $response;
    }

    // Ajax Response for Contact Us / Support Request form

    /**
     * @return AjaxResponse
     */
    public function openSupportRequestForm()
    {
        $response = new AjaxResponse();
        $modal_form = $this->formBuilder->getForm('\Drupal\user_provisioning\Form\MoUserProvisioningRequestSupport');
        $response->addCommand(new OpenModalDialogCommand('Support Request/Contact Us', $modal_form, ['width' => '40%']));
        return $response;
    }

}