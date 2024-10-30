=== Two-Factor Authentication - Clockwork SMS ===
Author: Clockwork
Website: http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=twofactor
Contributors: mediaburst, martinsteel, mediaburstjohnc
Tags: SMS, Clockwork, Clockwork SMS, Mediaburst, Security, Two-Factor Authentication, 2 Factor, Two Factor, Authentication, Access
Text Domain: clockwork_two_factor
Requires at least: 3.0.0
Tested up to: 4.0.0
Stable tag: 1.1.3
License: MIT

Proper security for your Wordpress site.

== Description ==

Controls access to your Wordpress administration panel by sending a code to your mobile phone when you try and login. Configurable for different user groups with a variety of options.

You need a [Clockwork SMS account](http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=twofactor) and some Clockwork credit to use this plugin.

= Requires =

* Wordpress 3 or higher

* A [Clockwork SMS account](http://www.clockworksms.com/platforms/wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=twofactor)

== Installation ==

1. Upload the 'clockwork-two-factor-authentication' directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter your Clockwork API key in the 'Clockwork Options' page under 'Clockwork SMS'
4. Set your options for Two-Factor Authentication

== Frequently Asked Questions ==

= What is a Clockwork API key? =

To send SMS you will need to sign up for a Clockwork SMS account
and purchase some SMS credit. When you sign up you'll be given an API key.

= Can I send to multiple mobile numbers? =

Yes, separate each mobile number with a comma.

= What format should the mobile number be in? =

All mobile numbers should be entered in international format without a
leading + symbol or international dialling prefix.

For example a UK number should be entered 447123456789, and a Republic
of Ireland number would be entered 353870123456.

= How do I disable the plugin? =

You can always override the plugin settings by setting <kbd>CLOCKWORK_TWOFACTOR</kbd> to <kbd>false</kbd> in your wp-config.php file.

== Screenshots ==

1. Settings for Clockwork two-factor authentication

2. Prompting for a password on login to the Administration panel

3. Not allowing access if you haven't set a mobile number

4. Not allowing access if you have entered an incorrect code

== Changelog ==

= 1.1.3 =
* Remove old branding

= 1.1.2 =
* Security Hardening

= 1.1.0 =
* Fix XSS Vulnerability

= 1.0.3 =

* Small coding stardards fix
* Tested with WordPress 4
* Clarify License (MIT)

= 1.0.2 =

* Fix form styling for WordPress 3.9

= 1.0.1 =

* WordPress 3.8 compatibility.

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

Code page styling will be a bit wonky unless you're running WordPress 3.9
