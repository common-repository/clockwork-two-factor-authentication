<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo bloginfo('name'); ?> › Authentication Error</title>
<link rel='stylesheet' id='buttons-css'  href='<?php echo site_url(); ?>/wp-includes/css/buttons.min.css?ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='open-sans-css'  href='//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='dashicons-css'  href='<?php echo site_url(); ?>/wp-includes/css/dashicons.min.css?ver=3.9' type='text/css' media='all' />
<link rel='stylesheet' id='login-css'  href='<?php echo site_url(); ?>/wp-admin/css/login.min.css?ver=3.9' type='text/css' media='all' />
<meta name="robots" content="noindex,nofollow">
</head>
<body class="login login-action-login wp-core-ui">
<div id="login">
	<h1><a href="http://wordpress.org/" title="Powered by WordPress"><?php echo bloginfo('name'); ?></a></h1>
  
  <div id="login_error">Two-factor authentication using Clockwork SMS is enabled on this Wordpress installation.</br ></br >However, you have not entered a mobile number into your profile. Please ask your administrator to do this.</div>
  
<script type="text/javascript">
function wp_attempt_focus(){
setTimeout( function(){ try{
d = document.getElementById('number');
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
