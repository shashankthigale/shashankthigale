<?php

namespace Drupal\user_provisioning\ProviderSpecific\Factory;

interface moResourceFactoryInterface {

  /**
   * @param $configured_application
   *
   * @return mixed
   */
  public function getAPIHandler();

  /**
   * @param $configured_application
   *
   * @return mixed
   */
  public function getParser();

  /**
   * @param $configured_application
   *
   * @return mixed
   */
  public function getResponseProcessor();
}
