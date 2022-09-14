<?php

namespace Drupal\user_provisioning\Form;

use Drupal;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user_provisioning\Helpers\moUserProvisioningAudits;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MoAuditsAndLogs extends FormBase
{
    private $base_url;
    private ImmutableConfig $config;
    private Config $config_factory;
    private LoggerInterface $logger;
    private moUserProvisioningAudits $audits;
    private Request $request;
    protected $messenger;


    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
        $this->config = Drupal::config('user_provisioning.settings');
        $this->config_factory = Drupal::configFactory()->getEditable('user_provisioning.settings');
        $this->logger = Drupal::logger('user_provisioning');
        $this->audits = new moUserProvisioningAudits();
        $this->request = Drupal::request();
        $this->messenger = Drupal::messenger();
    }

    /**
     * @inheritDoc
     */
    public function getFormId(): string
    {
        return "mo_audits_and_logs";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['mo_user_provisioning_add_css'] = array(
            '#attached' => array(
                'library' => array(
                    'user_provisioning/user_provisioning.admin',
                )
            ),
        );

        $form['mo_audits_and_logs']['header_top_style1'] = array(
            '#markup' => t('<div class="mo_user_provisioning_table_layout"><div class="mo_user_provisioning_container">
                         <div class="mo_user_provisioning_tab_heading">AUDITS AND LOGS<hr></div><br/> '),
        );

        $form['mo_audits_and_logs']['username'] = array(
            '#title' => 'Username',
            '#type' => 'search',
            '#default_value' => $this->getUsername(),
            '#default' => 'Search by uid or email',
        );


        $form['mo_audits_and_logs']['operation'] = array(
            '#title' => 'Operation',
            '#type' => 'select',
            '#default_value' => $this->getSelectedOperation(),
            '#options' => moUserProvisioningConstants::OPERATION_TYPES,
            '#prefix' => '<div class="mo_user_provisioning_same_line_div">',
            '#suffix' => '</div>',
            '#wrapper_attributes' => [
                'class' => ['container-inline'],
            ],
        );


        $form['mo_audits_and_logs']['status'] = array(
            '#title' => 'Status',
            '#type' => 'select',
            '#default_value' => $this->getSelectedStatus(),
            '#options' => moUserProvisioningConstants::STATUS_TYPES,
            '#prefix' => '<div class="mo_user_provisioning_same_line_div">',
            '#suffix' => '</div>',
            '#wrapper_attributes' => [
                'class' => ['container-inline'],
            ],
        );

        $form['mo_audits_and_logs']['no_of_rows'] = array(
            '#title' => 'No. of rows',
            '#type' => 'number',
            '#min' => 5,
            '#default_value' => $this->config->get('mo_audits_and_logs_no_of_rows'),
            '#prefix' => '<div class="mo_user_provisioning_same_line_div">',
            '#suffix' => '</div><br><br><br>',
            '#wrapper_attributes' => [
                'class' => ['container-inline'],
            ],
        );

        $form['mo_audits_and_logs']['filter'] = array(
            '#type' => 'submit',
            '#value' => t('Filter'),
            '#prefix' => '<br><br><div class="mo_user_provisioning_same_line_div">',
        );

        $form['mo_audits_and_logs']['reset'] = array(
            '#type' => 'submit',
            '#value' => t('Reset'),
            '#submit' => array('::resetFilter'),
        );

        $form['mo_audits_and_logs']['clear_logs'] = array(
            '#type' => 'submit',
            '#value' => t('Clear logs'),
            '#limit_validation_errors' => array(),
            '#submit' => array('::clearLogs'),
            '#suffix' => '</div>',
        );

        $row = $this->getCurrentLogs($this->getUsername());

        $rows = [];
        foreach ($row as $index => $value) {
            $value = (array)$value;
            $rows[$index + 1] = [
                'User ID' => $value['uid'],
                'Username' => $value['name'],
                'Date' => date("F j, Y, g:i a", $value['created']),
                'Operation' => $value['operation'],
                'Status' => $value['status'],
            ];
        }

        $form['mo_audits_and_logs']['table'] = array(
            '#type' => 'table',
            '#header' => array('User ID', 'Username', 'Date', 'Operation', 'Status'),
            '#rows' => $rows,
            '#responsive' => TRUE,
            '#sticky' => TRUE,
            '#empty' => t('No record found.'),
            '#size' => 3,
            '#prefix' => '<br/><br/>',
        );

        $form['mo_audits_and_logs']['pager'] = array(
            '#type' => 'pager',
        );

        return $form;
    }

    /**
     * Returns the entered username (fetches form the get parameter). Default is null
     * @return mixed|null
     */
    private function getUsername()
    {
        $username = $this->request->get('username');
        return empty($username) ? null : $username;
    }

    /**
     * Returns the selected operation type (fetches form the get parameter). Default is 'any'
     * @return mixed|string
     */
    private function getSelectedOperation()
    {
        $operation = $this->request->get('operation');
        return is_null($operation) ? 'any' : $operation;
    }

    /**
     * Returns the selected status type (fetches form the get parameter). Default is 'any'
     * @return mixed|string
     */
    private function getSelectedStatus()
    {
        $status = $this->request->get('status');
        return is_null($status) ? 'any' : $status;
    }

    /**
     * Fetch the logs from database and update the Audits and Logs table
     * @param $username Username to filter with
     * @return array|mixed|null
     */
    public function getCurrentLogs($username)
    {
        $status = $this->request->get('status');
        $operation = $this->request->get('operation');

        $filter_operation = true;
        if (is_null($operation) || $operation == 'any') {
            $filter_operation = false;
        }

        $filter_status = true;
        if (is_null($status) || $status == 'any') {
            $filter_status = false;
        }

        if ($filter_operation && $filter_status) {
            return $this->audits->filterBasedOnOperationAndStatus($operation, $status, $username);
        } elseif ($filter_operation) {
            return $this->audits->filterBasedOnOperation($operation, $username);
        } elseif ($filter_status) {
            return $this->audits->filterBasedOnStatus($status, $username);
        }

        $results = $this->audits->getAudits($username);
        return json_decode(json_encode($results), true);
    }

    /**
     * Reset the applied filter by removing the URL parameters.
     * @return void
     */
    function resetFilter()
    {
        $url = $this->base_url . moUserProvisioningConstants::AUDITS_AND_LOGS;
        $response = new RedirectResponse($url);
        $response->send();
        $this->messenger->addMessage($this->t('Filters has been reset successfully.'));
    }

    /**
     * Dumps all the logs by clearing the DB table
     * @return void
     */
    function clearLogs()
    {
        //TODO Future scope: Give an option to filter any specific type of log.
        $this->audits->clearTable();
        $this->messenger->addMessage($this->t('Logs has been cleared successfully.'));
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $number_of_rows = $form_state->getValue('no_of_rows');
        $this->config_factory->set('mo_audits_and_logs_no_of_rows', $number_of_rows)->save();

        $operation = $form_state->getValue('operation');
        $status = $form_state->getValue('status');
        $username = trim($form_state->getValue('username'));

        $url = $this->base_url . moUserProvisioningConstants::AUDITS_AND_LOGS
            . '?operation=' . $operation
            . '&status=' . $status
            . '&username=' . $username;

        $response = new RedirectResponse($url);
        $response->send();
    }
}
