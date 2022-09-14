<?php

namespace Drupal\user_provisioning\Helpers;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\StatementInterface;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Exception;
use Psr\Log\LoggerInterface;

class moUserProvisioningAudits
{
    private Connection $connection;
    private LoggerInterface $logger;
    private ImmutableConfig $config;

    public function __construct()
    {
        $this->connection = \Drupal::database();
        $this->logger = \Drupal::logger('user_provisioning');
        $this->config = \Drupal::config('user_provisioning.settings');
    }

    /**
     * Fetches and returns the Audits and logs from the DB.
     * @param $username
     * @return array Array of audits | null
     */
    public function getAudits($username)
    {
        try {
            $query = $this->connection->select(moUserProvisioningConstants::AUDIT_LOG_TABLE, 'audit_log')
                ->fields('audit_log', $this->getFields());

            if (!is_null($username)) {
                $query->condition('name', '%' . $username . '%', 'LIKE');
            }

            return (array)$query->orderBy('created', 'DESC')
                ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                ->limit(is_null($this->config->get('mo_audits_and_logs_no_of_rows')) ? 10 : $this->config->get('mo_audits_and_logs_no_of_rows'))
                ->execute()
                ->fetchAll();

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            //TODO handle this case by throwing an exception
            return null;
        }
    }

    /**
     * Returns an array containing the fields of the Audits and Logs table
     * @return string[]
     */
    public function getFields(): array
    {
        return array('uid', 'name', 'created', 'operation', 'status');
    }

    /**
     * Adds the audit entry in the database
     * @param array $values
     * @return StatementInterface|int|string|null
     */
    public function addAudit(array $values)
    {
        try {
            return $this->connection->insert(moUserProvisioningConstants::AUDIT_LOG_TABLE)
                ->fields($this->getFields(), $values)
                ->execute();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            //TODO handle this case by throwing an exception
            return null;
        }
    }

    public function clearTable()
    {
        $this->connection->delete(moUserProvisioningConstants::AUDIT_LOG_TABLE)->execute();
    }

    /**
     * Fetches and returns the filtered audits based on the operation value
     * @param string $operation Operation name
     * @param $username
     * @return array
     */
    public function filterBasedOnOperation(string $operation, $username)
    {
        try {
            $query = $this->connection->select(moUserProvisioningConstants::AUDIT_LOG_TABLE, 'audit_log')
                ->fields('audit_log', $this->getFields())
                ->condition('operation', $operation);

            if (!is_null($username)) {
                $query->condition('name', '%' . $username . '%', 'LIKE');
            }

            return (array)$query->orderBy('created', 'DESC')
                ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                ->limit(is_null($this->config->get('mo_audits_and_logs_no_of_rows')) ? 10 : $this->config->get('mo_audits_and_logs_no_of_rows'))
                ->execute()
                ->fetchAll();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            //TODO handle this case by throwing an exception
            return null;
        }
    }

    /**
     * Fetches and returns the filtered audits based on the status type
     * @param string $status Status type
     * @param $username
     * @return array
     */
    public function filterBasedOnStatus(string $status, $username)
    {
        try {
            $query = $this->connection->select(moUserProvisioningConstants::AUDIT_LOG_TABLE, 'audit_log')
                ->fields('audit_log', $this->getFields())
                ->condition('status', $status);
            if (!is_null($username)) {
                $query->condition('name', '%' . $username . '%', 'LIKE');
            }
            return (array)$query->orderBy('created', 'DESC')
                ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                ->limit(is_null($this->config->get('mo_audits_and_logs_no_of_rows')) ? 10 : $this->config->get('mo_audits_and_logs_no_of_rows'))
                ->execute()
                ->fetchAll();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            //TODO handle this case by throwing an exception
            return null;
        }
    }

    /**
     * Fetches and returns the filtered audits based on the status type and Operation name
     * @param string $operation Operation name
     * @param string $status Status type
     * @return array
     */
    public function filterBasedOnOperationAndStatus(string $operation, string $status, $username)
    {
        try {
            $query = $this->connection->select(moUserProvisioningConstants::AUDIT_LOG_TABLE, 'audit_log')
                ->fields('audit_log', $this->getFields())
                ->condition('operation', $operation)
                ->condition('status', $status);

            if (!is_null($username)) {
                $query->condition('name', '%' . $username . '%', 'LIKE');
            }

            return (array)$query->orderBy('created', 'DESC')
                ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                ->limit(is_null($this->config->get('mo_audits_and_logs_no_of_rows')) ? 10 : $this->config->get('mo_audits_and_logs_no_of_rows'))
                ->execute()
                ->fetchAll();

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            //TODO handle this case by throwing an exception
            return null;
        }
    }

}