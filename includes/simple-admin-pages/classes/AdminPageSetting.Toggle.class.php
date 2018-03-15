<?php

/**
 * Register, display and save an option with a single checkbox.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'label'			=> 'Label', 		// Checkbox label text
 *		);
 * );
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingToggle_2_1_0 extends sapAdminPageSetting_2_1_0 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
	
		$input_name = $this->get_input_name();

		?>

			<input type="checkbox" name="<?php echo $input_name; ?>" id="<?php echo $input_name; ?>" value="1"<?php if( $this->value == '1' ) : ?> checked="checked"<?php endif; ?>>
			

		<?php

		$this->display_description();

	}


}
