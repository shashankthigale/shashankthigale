<?php

namespace Drupal\user_provisioning\ProviderSpecific\EntityHandler;

interface moUserProvisioningEntityHandlerInterface
{
    /**
     * Search the resource at the configured application
     * @return array The response received from the configured application
     */
    public function searchResource(): array;

    /**
     * Creates the resource at the configured application
     * @return mixed
     */
    public function createResource();

    /**
     * Updates the resource at the configured application
     * @return mixed
     */
    public function updateResource();

    /**
     * Delete the resource at the configured application
     * @return mixed
     */
    public function deleteResource();
}
