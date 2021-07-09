<?php

/**
 * Class WP_Communibase_API
 */
class WP_Communibase_API
{

  /**
   *
   */
  const NAMESPACE = 'communibase/0.1';

  /**
   *
   */
  public static function init() {
    add_action('rest_api_init', function () {
      require_once __DIR__ . '/WP_Communibase_API_ValidateKey.php';
      (new WP_Communibase_API_ValidateKey())->register_routes();
    });
  }
}