<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin. This
 * file should primarily consist of HTML with a little bit of PHP.
 *
 * @link
 * @since      1.0.0
 *
 * @package    SPG_Projects
 * @subpackage SPG_Projects/admin/partials
 */
namespace SPG_Projects;

/**
 * Display settings on the admin menu page.
 *
 * @since    1.0.0
 */

function display_label( $for, $label) {
	?>

	<label for="<?php echo esc_attr( $for ); ?>"><?php echo esc_html( $label ); ?></label>
	<br>

	<?php
}

function display_text_input( $name, $value, $required = false ) {

	if ( $required ) {
		$required = 'required';
	} else {
		$required = '';
	}
	?>

	<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="text" value="<?php echo esc_attr( $value ) ?>" <?php echo $required; ?>/>
	<br>

	<?php
}

function display_project_type_radio_button( $name, $defaults ) {
	?>

	<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="current" <?php echo $defaults['current']; ?>/>
	Current Project<br>
	<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="recurring" <?php echo $defaults['recurring']; ?>/>
	Recurring Project<br>
	<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="past" <?php echo $defaults['past']; ?>/>
	Past Project<br>
	<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="oped" <?php echo $defaults['oped']; ?>/>
	Science Policy Op-Ed<br>

	<?php
}
