<?php

/**
 * Class WP_Communibase_SettingsPage
 */
class WP_Communibase_SettingsPage
{
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct()
  {
    add_action('admin_menu', array($this, 'add_plugin_menu'));
    add_action('admin_init', array($this, 'page_init'));
  }

  /**
   * Add options page
   */
  public function add_plugin_menu()
  {
    // for now we just put the settings as a sub menu in the settings menu
    // This page will be under "Settings"
    add_options_page(
      'Communibase',
      'Communibase',
      'manage_options',
      'communibase',
      array($this, 'create_admin_page')
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page()
  {
    if ( ! current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if ($_SERVER['HTTP_HOST'] === 'communibase-wordpress-plugin.localhost.kingsquare.eu:8080') {
      wp_enqueue_script('communibase-bundle-js', plugins_url('communibase/assets/bundle.dev.js'), [], null);
    } else {
      wp_enqueue_script('communibase-bundle-js', plugins_url('communibase/assets/bundle.min.js'), [], COMMUNIBASE_VERSION);
      wp_enqueue_style('communibase-bundle-css', plugins_url('communibase/assets/bundle.min.css'), [], COMMUNIBASE_VERSION);
    }

    // Set class property
    $this->options = get_option('communibase');
    ?>
    <div>
      <a target="_blank" rel="noopener" href="https://www.communibase.nl">
        <img src="<?php echo plugins_url('communibase/assets/siteLogo.png') ?>"/>
      </a>
    </div>

    <div id="communibase-plugin-app">

    </div>

    <div class="wrap">
      <form method="post" action="options.php">
        <?php
        // This prints out all hidden setting fields
        settings_fields('communibase');
        do_settings_sections('communibase');
        ?>
        <?php
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * Register and add settings
   */
  public function page_init()
  {

    // whitelist options
    register_setting('communibase', 'communibase', array($this, 'sanitize'));

    add_settings_section(
      'communibase_section_id', // ID
      '', // Title
      '', // Callback
      'communibase' // Page
    );

    add_settings_field(
      'api_key', // ID
      'API Key', // Title
      array($this, 'renderFieldApiKey'), // Callback
      'communibase', // Page
      'communibase_section_id' // Section
    );

    add_settings_section(
      'communibase_section_deviant', // ID
      '', // Title
      array($this, 'print_deviant_section_info'), // Callback
      'communibase' // Page
    );

    add_settings_field(
      'api_custom_url',
      'Use deviant API',
      array($this, 'renderFieldUseProdApi'), // Callback
      'communibase',
      'communibase_section_deviant'
    );

    add_settings_field(
      'api_url',
      'API URL',
      array($this, 'renderFieldApiUrl'), // Callback
      'communibase',
      'communibase_section_deviant'
    );

    add_settings_field(
      'api_url_custom',
      'Custom API URL',
      array($this, 'renderFieldCustomApiUrl'), // Callback
      'communibase',
      'communibase_section_deviant'
    );

    add_settings_field(
      'api_host',
      'Custom API URL Host',
      array($this, 'renderFieldCustomApiHost'), // Callback
      'communibase',
      'communibase_section_deviant'
    );
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   *
   * @return array
   */
  public function sanitize($input)
  {
    $new_input = array();
    if (isset($input['api_key'])) {
      $new_input['api_key'] = sanitize_text_field($input['api_key']);
    }

    if (isset($input['api_url'])) {
      $new_input['api_url'] = sanitize_text_field($input['api_url']);
    }

    if (isset($input['api_host'])) {
      $new_input['api_host'] = sanitize_text_field($input['api_host']);
    }

    return $new_input;
  }

  /**
   * Print the Section text
   */
  public function print_main_section_info()
  {
    print 'Enter your connection settings';
  }

  /**
   * Print the Section text
   */
  public function print_deviant_section_info()
  {
    print 'In some cases you may need to use a deviant API, in this case set the following';
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldApiKey()
  {
    printf(
      '<input type="text" id="communibase_api_key" name="communibase[api_key]" value="%s" placeholder="" />' .
      '<div class="communibase-api-key-check"></div>',
      isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
    );
  }

  const API_PRODUCTION = 'https://api.communibase.nl/0.1/';

  /**
   *
   */
  public function renderFieldUseProdApi()
  {
    $isProductionApi = empty($this->options['api_url']) || $this->options['api_url'] === self::API_PRODUCTION;
    echo '<input type="checkbox" id="communibase_use_deviant_api" name="communibase[use_deviant_api]" ' .
      ($isProductionApi ? '' : 'checked="checked"') .
      '/>';
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldApiUrl()
  {
    // <input type="text" id="communibase_api_url" name="communibase[api_url]" value="%s" placeholder="https://api.communibase.nl/0.1/"/>
    printf(
      '<select id="communibase_api_url" name="communibase[api_url]" >' .
      '<option value="https://api.communibase.nl/0.1/">PRODUCTION (https://api.communibase.nl/0.1/)</option>' .
      '<option value="https://api.staging.communibase.nl/0.1/">STAGING (https://api.staging.communibase.nl/0.1/)</option>' .
      '<option value="-1">CUSTOM</option>' .
      '</select>',
      isset($this->options['api_url']) ? esc_attr($this->options['api_url']) : ''
    );
  }

  public function renderFieldCustomApiUrl()
  {
    printf(
      '<input type="text" id="communibase_api_custom_url" name="communibase[api_url]" value="%s" placeholder="https://api.communibase.nl/0.1/"/>',
      isset($this->options['api_url']) ? esc_attr($this->options['api_url']) : ''
    );
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldCustomApiHost()
  {
    printf(
      '<input type="text" id="communibase_api_custom_host" name="communibase[api_host]" value="%s" />',
      isset($this->options['api_host']) ? esc_attr($this->options['api_host']) : ''
    );
  }
}
