<?php

namespace Drupal\user_provisioning\Helpers;


use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use Psr\Log\LoggerInterface;

class moUserProvisioningLogger
{
    protected LoggerInterface $logger;
    private ImmutableConfig $config;

    public function __construct()
    {
        $this->logger = Drupal::logger('user_provisioning');
        $this->config = Drupal::config('user_provisioning.settings');
    }

    /**
     * @param array $content The content that will be added to the log.
     * @param int $line The line on which the log is initiated __LINE__
     * @param string $function The function in which the log is initiated __FUNCTION__
     * @param string $file The file in which the log is initiated __FILE__
     * @param string $description Addition description related to the log. Refer to the function description.
     * @return void
     */
    public function addFormattedLog($content, int $line, string $function, string $file, string $description = '')
    {
        if ($this->isLoggingEnabled()) {
            $this->logger->info($file . '-' . $function . '()- ' . $line . ': ' . $description . '<pre><code>' . print_r($content, TRUE) . '</code></pre>');
        }
    }

    /**
     * @param string $content The content that will be added to the log.
     * @param int $line The line on which the log is initiated __LINE__
     * @param string $function The function in which the log is initiated __FUNCTION__
     * @param string $file The file in which the log is initiated __FILE__
     * @return void
     */
    public function addLog(string $content, int $line, string $function, string $file)
    {
        if ($this->isLoggingEnabled()) {
            $this->logger->info($file . '-' . $function . '()- ' . $line . ': ' . $content);
        }
    }

    /**
     * Check if the option to add logs is enabled.
     * @return bool
     */
    private function isLoggingEnabled(): bool
    {
        if ($this->config->get('mo_user_provisioning_enable_loggers') == true) {
            return true;
        }
        return false;
    }
}