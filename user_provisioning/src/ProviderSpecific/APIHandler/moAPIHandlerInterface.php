<?php

namespace Drupal\user_provisioning\ProviderSpecific\APIHandler;

interface moAPIHandlerInterface
{

    /**
     * @param $resource_id
     *
     * @return mixed
     */
    public function get(string $query);

    /**
     * @param array $body
     *
     * @return mixed
     */
    public function post(array $body);

    /**
     * @param array $body
     *
     * @return mixed
     */
    public function put(array $body);

    /**
     * @param array $patch
     *
     * @return mixed
     */
    public function patch(array $patch);

    /**
     * @param $resource_id
     *
     * @return mixed
     */
    public function delete($resource_id);

}
