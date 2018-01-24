<?php

/**
 * Register, display and save a color field setting in the admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingColor_2_1_0 extends sapAdminPageSetting_2_1_0 {

	public $sanitize_callback = 'sanitize_text_field';
	public $default_value = '';

	/**
	 * Scripts that must be loaded for this component
	 * @since 2.1.0
	 */
	public $scripts = array(
		'sap-color' => array(
			'path'			=> 'js/color.js',
			'dependencies'	=> array( 'wp-color-picker' ),
			'version'		=> '2.1.0',
			'footer'		=> true,
		),
	);

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
	    $val = empty($this->value) ? $this->default_value : $this->value;
		?>
			<input class="sap-color-field" name="<?php echo $this->get_input_name(); ?>" type="input" id="<?php echo $this->id; ?>" value="<?php echo $val; ?>" data-default-color="<?php echo $this->default_value; ?>">
		<?php

		$this->display_description();

	}

}
