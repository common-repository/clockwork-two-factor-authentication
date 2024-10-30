<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<h3>Two-Factor Authentication Settings</h3>

	<table class="form-table">

		<tr>
			<th><label for="mobile">Mobile Number</label></th>

			<td>
        <input type="text" name="mobile" id="mobile" value="<?php echo esc_attr( get_the_author_meta( 'mobile', $data->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your mobile number in international format, with no leading zeroes e.g. 447123123456.</span>
			</td>
		</tr>

	</table>
