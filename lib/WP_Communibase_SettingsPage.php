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
    add_action('admin_menu', array($this, 'add_plugin_page'));
    add_action('admin_init', array($this, 'page_init'));
  }

  /**
   * Add options page
   */
  public function add_plugin_page()
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
    // Set class property
    $this->options = get_option('communibase');
    ?>
    <div>
      <a target="_blank" rel="noopener" href="https://www.communibase.nl">
        <img src="<?php echo plugins_url('communibase/assets/siteLogo.png') ?>" />
      </a>
    </div>
    <div class="wrap">
      <form method="post" action="options.php">
        <?php
        // This prints out all hidden setting fields
        settings_fields('communibase');
        do_settings_sections('communibase');
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
      'Settings', // Title
      array($this, 'print_section_info'), // Callback
      'communibase' // Page
    );

    add_settings_field(
      'api_key', // ID
      'API Key', // Title
      array($this, 'renderFieldApiKey'), // Callback
      'communibase', // Page
      'communibase_section_id' // Section
    );

    add_settings_field(
      'api_url',
      'API URL',
      array($this, 'renderFieldApiUrl'), // Callback
      'communibase',
      'communibase_section_id'
    );

    add_settings_field(
      'api_host',
      'API URL Host',
      array($this, 'renderFieldApiHost'), // Callback
      'communibase',
      'communibase_section_id'
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
  public function print_section_info()
  {
    print 'Enter your connection settings below:';
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldApiKey()
  {
    printf(
      '<input type="text" id="communibase_api_key" name="communibase[api_key]" value="%s" placeholder="" />',
      isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
    );
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldApiUrl()
  {
    printf(
      '<input type="text" id="communibase_api_url" name="communibase[api_url]" value="%s" placeholder="https://api.communibase.nl/0.1/"/>',
      isset($this->options['api_url']) ? esc_attr($this->options['api_url']) : ''
    );
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function renderFieldApiHost()
  {
    printf(
      '<input type="text" id="communibase_api_host" name="communibase[api_host]" value="%s" />',
      isset($this->options['api_host']) ? esc_attr($this->options['api_host']) : ''
    );
  }
}
