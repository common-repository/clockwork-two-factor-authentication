<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo bloginfo('name'); ?> › Enter Code</title>
<link rel='stylesheet' id='buttons-css'  href='<?php echo site_url(); ?>/wp-includes/css/buttons.min.css?ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='open-sans-css'  href='//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='dashicons-css'  href='<?php echo site_url(); ?>/wp-includes/css/dashicons.min.css?ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='login-css'  href='<?php echo site_url(); ?>/wp-admin/css/login.min.css?ver=3.9' type='text/css' media='all' />
<meta name="robots" content="noindex,nofollow">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" >
jQuery(document).ready(function($) {
  $('#resendcode').click( function(e) {
    window.location.href = window.location.href.replace( /\?code.*/, '' );
  });
  
  $('form#authform').submit( function(e) {
    // e.preventDefault();
    // if( window.location.href.replace( /code.*/, '' ) )
    // window.location.href = window.location.href.replace( /\/?wp\-login\/.*/, '' ) + '/wp-admin/?code=' + $('input#code').val() + '&user=' + $('input#user').val();  
  });
});
</script>
</head>
<body class="login login-action-login wp-core-ui">
<div id="login">
	<h1><a href="http://wordpress.org/" title="Powered by WordPress"><?php echo bloginfo('name'); ?></a></h1>
  
<?php if( isset( $data['message'] ) ) { ?><p class="message"><?php echo $data['message']; ?></p><?php } ?>
<?php if( isset( $data['error_message'] ) ) { ?><div id="login_error"><?php echo $data['error_message']; ?></div><?php } ?>

<form name="authform" id="authform" action="" method="get">
<p>
	<label for="user_login">Code<br>
	<input type="text" name="code" id="code" class="input" value="" size="20" maxlength="4" tabindex="10"></label>
</p>
<p class="submit">
	<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In" tabindex="100">
	<input type="hidden" name="redirect_to" value="<?php echo site_url(); ?>/wp-admin/">
	<input type="hidden" name="testcookie" value="1">
</p>
</form>

<p id="nav">
<a href="" id="resendcode" title="Resend Code">Resend Code</a>
</p>

<script type="text/javascript">
function wp_attempt_focus(){
setTimeout( function(){ try{
d = document.getElementById('user_login');
d.focus();
d.select();
} catch(e){}
}, 200);
}

if(typeof wpOnload=='function')wpOnload();
</script>

<p id="backtoblog"><a href="<?php echo site_url(); ?>/" title="Are you lost?">← Back to Wordpress Test</a></p>
	
</div>

	
	<div class="clear"></div>
	
	
</body></html>
