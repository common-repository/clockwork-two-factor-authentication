<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">

    <div class="icon32"><img src="<?php echo plugins_url( 'images/logo_32px_32px.png', dirname( __FILE__ ) ); ?>" /></div>
    <h2>Two-Factor SMS Options</h2>

    <form method="post" action="options.php">
    <?php settings_fields( 'clockwork_two_factor_user' ); ?>
    <?php do_settings_sections( 'clockwork_two_factor_user' ); ?>
    <?php submit_button(); ?>
    </form>

    <form method="post" action="options.php">
    <?php settings_fields( 'clockwork_two_factor_credit' ); ?>
    <?php do_settings_sections( 'clockwork_two_factor_credit' ); ?>
    <?php submit_button(); ?>
    </form>

</div>
