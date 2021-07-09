<?php

/**
 * Class WP_Communibase_API_ValidateKey
 */
class WP_Communibase_API_ValidateKey extends \WP_REST_Controller
{

  /**
   * Register route(s)
   */
  public function register_routes()
  {
    register_rest_route(WP_Communibase_API::NAMESPACE, '/validateKey', array(
      'methods' => 'POST',
      'callback' => array( $this, 'post' ),
      'args' => array(
        'key' => array(
          'type' => 'string',
          'required' => true,
          'sanitize_callback' => 'sanitize_text_field'
        )
      ),
      'permissions_callback' => array( $this, 'permissions' ),
    ));
  }

  /**
   * Check request permissions
   *
   * @return bool
   */
  public function permissions()
  {
    return current_user_can( 'manage_options' );
  }

  /**
   * @param WP_REST_Request $req
   *
   * @return WP_Error|WP_REST_Response
   */
  public function post(\WP_REST_Request $req)
  {
    try {
      WP_Communibase_Connector::getInstance()->search('Person2', [], ['limit' => 1]);
      return new \WP_REST_Response(true, 200);
    } catch (\Exception $ex) {
      return new \WP_Error('error', $ex->getMessage(), array('status' => 500));
    }
  }
}
