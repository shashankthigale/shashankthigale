<?php

namespace Drupal\user_provisioning\ProviderSpecific\Parsers;

use Drupal\Core\Entity\EntityInterface;

interface moResourceParserInterface
{
    /**
     * @param $resource_id
     * @return string
     */
    public function search($resource_id): string;

    /**
     * @param $resource_id
     * @return mixed
     */
    public function get($resource_id);

    /**
     * @param array $resource
     * @return mixed
     */
    public function post(EntityInterface $entity);

    /**
     * @param array $resource
     * @return mixed
     */
    public function put(array $resource);

    /**
     * @param array $resource
     * @return mixed
     */
    public function patch(array $resource);

    /**
     * @param array $resource
     * @return mixed
     */
    public function delete(array $resource);
}
