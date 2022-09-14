<?php

namespace Drupal\user_provisioning\ProviderSpecific\ResponseProcessor;

use Psr\Http\Message\ResponseInterface;

interface moResourceResponseProcessorInterface
{

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    public function get(ResponseInterface $response);

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    public function post(ResponseInterface $response);

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    public function patch(ResponseInterface $response);

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    public function put(ResponseInterface $response);

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    public function delete(ResponseInterface $response);
}
