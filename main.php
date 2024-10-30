<?php
class Clockwork_Two_Factor_Plugin extends Clockwork_Plugin {

  protected $plugin_name = 'Two-Factor SMS';
  protected $language_string = 'clockwork_two_factor';
  protected $prefix = 'clockwork_two_factor';
  protected $folder = '';

  protected $forms = array();

  /**
   * Constructor: setup callbacks and plugin-specific options
   *
   * @author James Inman
   */
  public function __construct() {
    parent::__construct();

    // Set the plugin's Clockwork SMS menu to load the contact forms
    $this->plugin_callback = array( $this, 'clockwork_two_factor' );
    $this->plugin_dir = basename( dirname( __FILE__ ) );

    // Handle extra profile fields
    add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );
    add_action( 'edit_user_profile', array( $this, 'show_user_profile' ) );
    add_action( 'personal_options_update', array( $this, 'save_user_profile' ) );
    add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ) );
    add_action( 'clear_auth_cookie', array( $this, 'destroy_code' ) );

    // Actually send the code
    add_action( 'wp_login', array( $this, 'send_code' ), 10, 2 );
  }

  /**
   * Setup the admin navigation
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_navigation() {
    parent::setup_admin_navigation();
  }

  /**
   * Display an error if I don't have a mobile number set
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_message() {
    parent::setup_admin_message();


    $user = wp_get_current_user();
    $mobile = get_user_meta( $user->ID, 'mobile', true );

    if( !isset( $mobile ) || $mobile == '' ) {
      $this->show_admin_message( 'Clockwork Two-Factor Authentication: if you do not <a href="profile.php#mobile">set your mobile number</a> you are in danger of being locked out of your Wordpress installation!', true);
    }
  }

  /**
   * Setup HTML for the admin <head>
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_head() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'css/clockwork.css', __FILE__ ) . '">';
  }

  /**
   * Register the settings for this plugin
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_init() {
    // Register main Clockwork functions
    parent::setup_admin_init();

    // Plugin options
    register_setting( 'clockwork_two_factor_user', 'clockwork_two_factor_user', array( $this, 'validate_clockwork_user_options' ) );
    add_settings_section( 'clockwork_two_factor_user', 'User Settings', array( $this, 'user_settings_text' ), 'clockwork_two_factor_user' );
    add_settings_field( 'enabled', 'Enable For', array( $this, 'clockwork_two_factor_enabled_input' ), 'clockwork_two_factor_user', 'clockwork_two_factor_user' );
    register_setting( 'clockwork_two_factor_credit', 'clockwork_two_factor_credit' );
    add_settings_section( 'clockwork_two_factor_credit', 'Credit Settings', array( $this, 'credit_settings_text' ), 'clockwork_two_factor_credit' );
    add_settings_field( 'credit', 'Credit Option', array( $this, 'clockwork_two_factor_credit_options' ), 'clockwork_two_factor_credit', 'clockwork_two_factor_credit' );

    // Default to disabling the plugin
    if( !get_option( 'clockwork_two_factor_credit' ) ) {
      update_option( 'clockwork_two_factor_credit', array( 'credit' => 'disable_plugin' ) );
    }

    // Load the Two-Factor authentication before we do anything else
    $this->handle_two_factor_authentication();
  }

  /**
   * Actually send the code out
   *
   * @param string $logged_in_cookie Ignore
   * @param string $expire Ignore
   * @param string $expiration Ignore
   * @param string $user_id The user's ID
   * @param string $logged_in Ignore
   * @return void
   * @author James Inman
   */
  public function send_code( $wp_login, $user ) {
    // Get the mobile number
    $mobile_number = get_user_meta( $user->ID, 'mobile', true );

    // Do we have a Clockwork API key set?
    $options = get_option( 'clockwork_options' );
    if( !is_array( $options ) || !isset( $options['api_key'] ) || empty( $options['api_key'] ) ) {
      print 1;
      return;
    }

    // Do we have the disable override setup in wp-config.php?
    if( defined( 'CLOCKWORK_TWOFACTOR' ) ) {
      if( constant( 'CLOCKWORK_TWOFACTOR' ) === false ) {
        return;
      }
    }

    // Get the enabled groups and credit options
    if( is_array( get_option( 'clockwork_two_factor_user' ) ) ) {
      $options = @array_merge( $options, get_option( 'clockwork_two_factor_user' ) );
    }
    if( is_array( get_option( 'clockwork_two_factor_credit' ) ) ) {
      $options = @array_merge( $options, get_option( 'clockwork_two_factor_credit' ) );
    }

    // Have we enabled any groups?
    if( !isset( $options['enabled'] ) ) {
      return;
    }

    // Is this user not in a group which we're using two-factor authentication for?
    if( count( array_intersect( array_values( $user->roles ), array_keys( $options['enabled'] ) ) ) == 0 ) {
      return;
    }

    // Do we have any credit?
    $clockwork = new WordPressClockwork( $options['api_key'] );
    $balance = $clockwork->checkBalance();

    // If we're out of credit, check what the option is
    if( floatval( $balance['balance'] ) == 0.0 ) {
      if( !isset( $options['credit'] ) || $options['credit'] == 'disable_plugin' ) {
        return;
      } elseif( $options['credit'] == 'disable_wordpress' ) {
        $message = '<p>You cannot login to your Wordpress install as you have run out of Clockwork credit. To override this, set <kbd>CLOCKWORK_TWOFACTOR</kbd> to false in your wp-config.php file.</p><p>To buy more credit, visit <a href="https://app.clockworksms.com/purchase?utm_source=wpadmin&utm_medium=plugin&utm_campaign=twofactor">http://www.clockworksms.com/</a>.</p>';
        wp_logout();
        $this->die_with_message( $message );
        die();
      }
    }

    // Does this user have a mobile number set?
    if( !isset( $mobile_number ) || ( $mobile_number == '' ) ) {
      $this->render_template( 'required-number-form' );
      wp_logout();
      die();
    }

    // Send them the code
    $code = $this->generate_code();
    $message = 'Your Clockwork SMS code for ' . get_bloginfo('name') . ' is ' . $code . '.';
  	update_user_meta( $user->ID, 'clockwork_code', $code );
  	update_user_meta( $user->ID, 'clockwork_time', time() );

    // Send the message
    try {
      $messages = array( array( 'from' => $options['from'], 'to' => $user->mobile, 'message' => $message ) );
      $result = $clockwork->send( $messages );
      update_user_meta( $user->ID, 'clockwork_prevent_login', '1' );
    } catch( ClockworkException $e ) {
      $result = "Error: " . $e->getMessage();
    } catch( Exception $e ) {
      $result = "Error: " . $e->getMessage();
    }
  }

  /**
   * The main handler for the two-factor authentication
   *
   * @return void
   * @author James Inman
   */
  public function handle_two_factor_authentication() {
    // Do we have the disable override setup in wp-config.php?
    if( defined( 'CLOCKWORK_TWOFACTOR' ) ) {
      if( constant( 'CLOCKWORK_TWOFACTOR' ) === false ) {
        return;
      }
    }

    $user = wp_get_current_user();

    // Have we entered a code?
    if( isset( $_GET['code'] ) ) {
      $code = get_user_meta( $user->ID, 'clockwork_code', true );
      if( $code == $_GET['code'] ) {
        $this->destroy_code();
        update_user_meta( $user->ID, 'clockwork_prevent_login', '0' );
        return;
      } else {
        $this->render_template( 'code-form', array( 'error_message' => "Your code was entered incorrectly. Please try again." ) );
        die();
      }
    }

    // If no code entered, can we login?
    $meta = get_user_meta( $user->ID, 'clockwork_prevent_login', true );
    if( ( isset( $meta ) && $meta == 1 ) || !isset( $meta ) ) {
      $this->render_template( 'code-form', array( 'user' => $user, 'message' => "You have been sent a 4-digit authentication code to your phone. Enter it below." ) );
      die();
    }

  }

  /**
   * Generate the authentication code
   *
   * @return void
   * @author James Inman
   */
  public function generate_code() {
    mt_srand( time() );
    return mt_rand( 1000, 9999 );
  }

  /**
   * Destroy all Clockwork codes on logout
   *
   * @return void
   * @author James Inman
   */
  public function destroy_code() {
    $user = wp_get_current_user();
  	update_user_meta( $user->ID, 'clockwork_time', '0' );
  	update_user_meta( $user->ID, 'clockwork_code', '0' );
    return;
  }

  /**
   * Run wp_die with a given title and message
   *
   * @param string $message
   * @return void
   * @author James Inman
   */
  public function die_with_message( $message ) {
    $title = get_bloginfo('name') . ' - Two-Factor SMS Authentication';
    wp_die( $message, $title );
    return;
  }

  /**
   * Text for the user settings
   *
   * @return void
   * @author James Inman
   */
  public function user_settings_text() {
    print '<p>' . __( "You can set which roles to enable two-factor authentication for, and require all users in these groups without a mobile number entered on their profile to enter one next time they login." ) . '</p>';
  }

  /**
   * Text for the credit settings
   *
   * @return void
   * @author James Inman
   */
  public function credit_settings_text() {
    print '<p>' . __( "You can set the options for when your run out of credit. You can always override these settings by setting <kbd>CLOCKWORK_TWOFACTOR</kbd> to <kbd>false</kbd> in your wp-config.php file." ) . '</p>';
  }

  /**
   * Show the roles to enable for
   *
   * @return void
   * @author James Inman
   */
  public function clockwork_two_factor_enabled_input() {
    $options = get_option( 'clockwork_two_factor_user' );
    if( $_GET['settings-updated'] && isset( $options['error_message'] ) ) {
      print '<div id="message" class="error"><p><strong>' . $options['error_message'] . '</strong></p></div>';
    }

    $roles = get_editable_roles();
    foreach( $roles as $tag => $data ) {
      if( isset( $options['enabled'][$tag] ) ) {
        print '<label><input type="checkbox" checked="checked" name="clockwork_two_factor_user[enabled][' . $tag . ']" id="clockwork_two_factor_user_enabled" value="1">&nbsp;&nbsp;&nbsp;' . $data['name'] . '</label><br />';
      } else {
        print '<label><input type="checkbox" name="clockwork_two_factor_user[enabled][' . $tag . ']" id="clockwork_two_factor_user_enabled" value="1">&nbsp;&nbsp;&nbsp;' . $data['name'] . '</label><br />';
      }
    }
  }

  /**
   * Validate the groups: only enable the plugin if an admin has a mobile number set
   *
   * @param string $val
   * @return void
   * @author James Inman
   */
  public function validate_clockwork_user_options( $val ) {
    $all_users = new WP_User_Query( array( 'role' => 'Administrator', 'fields' => 'all_with_meta' ) );
    $mobile_number_set = 0;

    foreach( $all_users->results as $u ) {
      if( $u->has_prop( 'mobile' ) ) {
        $mobile_number_set++;
      }
    }

    if( $mobile_number_set == 0 ) {
      $val = array();
      $val['error_message'] = 'At least one of your administrators must have a mobile number set.';
      return $val;
    }

    return $val;
  }

  /**
   * Require a mobile number from all enabled users
   *
   * @return void
   * @author James Inman
   */
  public function clockwork_two_factor_require_number_input() {
    $options = get_option( 'clockwork_two_factor_user' );
    if( isset( $options['require_number'] ) ) {
      print '<input type="checkbox" name="clockwork_two_factor_user[require_number]" checked="checked" id="clockwork_two_factor_user_require_number" value="1">';
    } else {
      print '<input type="checkbox" name="clockwork_two_factor_user[require_number]" id="clockwork_two_factor_user_require_number" value="1">';
    }
  }

  public function clockwork_two_factor_credit_options() {
    $options = get_option( 'clockwork_two_factor_credit' );
    if( !isset( $options ) || !isset( $options['credit'] ) || $options['credit'] == 'disable_plugin' ) {
      print '<label><input type="radio" name="clockwork_two_factor_credit[credit]" value="disable_plugin" checked="checked">&nbsp;&nbsp;&nbsp;Disable the plugin when you run out of text message credit. <strong>This will leave your installation insecure until you top up.</strong></label><br />';
      print '<label><input type="radio" name="clockwork_two_factor_credit[credit]" value="disable_wordpress">&nbsp;&nbsp;&nbsp;Disable access to your Wordpress installation when you run out of text message credit. <strong>Selecting this option will lock you out of your Wordpress installation until you top up.</strong></label><br />';
    } else {
      print '<label><input type="radio" name="clockwork_two_factor_credit[credit]" value="disable_plugin">&nbsp;&nbsp;&nbsp;Disable the plugin when you run out of text message credit. <strong>This will leave your installation insecure until you top up.</strong></label><br />';
      print '<label><input type="radio" name="clockwork_two_factor_credit[credit]" value="disable_wordpress" checked="checked">&nbsp;&nbsp;&nbsp;Disable access to your Wordpress installation when you run out of text message credit. <strong>Selecting this option will lock you out of your Wordpress installation until you top up.</strong></label><br />';
    }
  }

  /**
   * Show the additional mobile number fields for the user
   *
   * @param string $user
   * @return void
   * @author James Inman
   */
  public function show_user_profile( $user ) {
    $this->render_template( 'user-options', $user );
  }

  /**
   * Save the additional user profile fields
   *
   * @param string $user_id
   * @return void
   * @author James Inman
   */
  public function save_user_profile( $user_id ) {
  	if ( !current_user_can( 'edit_user', $user_id ) ) {
  		return false;
    }

  	update_user_meta( $user_id, 'mobile', sanitize_text_field($_POST['mobile']) );
  }

  /**
   * Function to provide a callback for the main plugin action page
   *
   * @return void
   * @author James Inman
   */
  public function clockwork_two_factor() {
    $this->render_template( 'two-factor-options' );
  }

  /**
   * Check if username and password have been entered
   *
   * @return void
   * @author James Inman
   */
  public function get_existing_username_and_password() { }

}

$cp = new Clockwork_Two_Factor_Plugin();
