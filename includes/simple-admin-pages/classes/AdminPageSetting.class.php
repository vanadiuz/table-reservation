<?php

/**
 * Register, display and save a setting on a custom admin menu
 *
 * All settings accept the following arguments in their constructor functions.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description' 	// Help text description
 * 		'args'			=> array();			// Arguments to pass to WordPress's add_settings_field() function
 * );
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

abstract class sapAdminPageSetting_2_1_0 {

	// Page defaults
	public $id; // used in form fields and database to track and store setting
	public $title; // setting label
	public $description; // optional description of the setting
	public $value; // value of the setting, if a value exists

	/**
	 * An array of arguments accepted by add_settings_field.
	 * See: https://codex.wordpress.org/Function_Reference/add_settings_field
	 */
	public $args = array();

	// Array to store errors
	public $errors = array();

	/**
	 * Position in section
	 *
	 * An array with two elements describing where this setting should
	 * be placed in its section. The first element describes a position
	 * and the second (optional) element identifies the id of an
	 * existing setting. Examples:
	 *
	 * array( 'bottom' ) // Default. bottom of section
	 * array( 'top' ) // top of section
	 * array( 'before', 'my-setting' ) // before a specific setting
	 * array( 'after', 'my-setting' ) // after a specific setting
	 *
	 * This setting is intended for use when you have to hook in after
	 * the settings page has been defined, such as adding a new setting
	 * from a third-party plugin.
	 */
	public $position;

	/**
	 * Function to use when sanitizing the data
	 *
	 * We set this to a strict sanitization function as a default, but a
	 * setting should override this in an extended class when needed.
	 *
	 * @since 1.0
	 */
	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Scripts that must be loaded for this component
	 * @since 2.0.a.4
	 */
	public $scripts = array(
		/**
		 * Example
		 * See: http://codex.wordpress.org/Function_Reference/wp_enqueue_script
		 *
		'handle' => array(
			'path'			=> 'path/from/simple-admin-pages/file.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> '3.5.0',
			'footer'		=> true,
		),
		 */
	);

	/**
	 * Styles that must be loaded for this component
	 * @since 2.0.a.4
	 */
	public $styles = array(
		/**
		 * Example
		 * See: http://codex.wordpress.org/Function_Reference/wp_enqueue_style
		 *
		'handle' => array(
			'path'			=> 'path/from/simple-admin-pages/file.css',
			'dependencies'	=> 'array( 'another-handle')', // or empty string
			'version'		=> '3.5.0',
			'media'			=> null,
		),
		 */
	);

	/**
	 * Translateable strings required for this component
	 *
	 * Settings classes which require translateable strings should be
	 * defined with string id's pointing to null values. The actual
	 * strings should be passed with the $sap->add_setting() call.
	 *
	 * @since 2.0.a.8
	 */
	public $strings = array(
		/**
		 * Example
		 *
		 'string_id' => null
		 */
	);

	/**
	 * Initialize the setting
	 *
	 * By default, every setting takes an id, title and description in the $args
	 * array.
	 *
	 * @since 1.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );

		// Get any existing value
		$this->set_value();

		// Check for missing data
		$this->missing_data();
	}

	/**
	 * Parse the arguments passed in the construction and assign them to
	 * internal variables. This function will be overwritten for most subclasses
	 * @since 1.0
	 */
	private function parse_args( $args ) {
		foreach ( $args as $key => $val ) {
			switch ( $key ) {

				case 'id' :
					$this->{$key} = esc_attr( $val );

				default :
					$this->{$key} = $val;

			}
		}
	}

	/**
	 * Check for missing data when setup.
	 * @since 1.0
	 */
	private function missing_data() {

		$error_type = 'missing_data';

		// Required fields
		if ( empty( $this->id ) ) {
			$this->set_error(
				array(
					'type'		=> $error_type,
					'data'		=> 'id'
				)
			);
		}
		if ( empty( $this->title ) ) {
			$this->set_error(
				array(
					'type'		=> $error_type,
					'data'		=> 'title'
				)
			);
		}

		// Check for strings
		foreach ( $this->strings as $id => $string ) {

			if ( $string === null ) {
				$this->set_error(
					array(
						'type'		=> $error_type,
						'data'		=> 'string: ' . $id,
					)
				);
			}
		}
	}

	/**
	 * Set a value
	 * @since 2.0
	 */
	public function set_value( $val = null ) {

		if ( $val === null ) {
			$option_group_value = get_option( $this->page );
			$val = isset( $option_group_value[ $this->id ] ) ? $option_group_value[ $this->id ] : '';
		}

		$this->value = $this->esc_value( $val );
	}

	/**
	 * Escape the value to display it in text fields and other input fields
	 *
	 * We use esc_attr() here so that the default is quite strict, but other
	 * setting types should override this function with the appropriate escape
	 * function. See: http://codex.wordpress.org/Data_Validation
	 *
	 * @since 1.0
	 */
	public function esc_value( $val ) {
		return esc_attr( $val );
	}

	/**
	 * Wrapper for the sanitization callback function.
	 *
	 * This just reduces code duplication for child classes that need a custom
	 * callback function.
	 * @since 1.0
	 */
	public function sanitize_callback_wrapper( $value ) {
		return call_user_func( $this->sanitize_callback, $value );
	}

	/**
	 * Display this setting
	 * @since 1.0
	 */
	abstract public function display_setting();

	/**
	 * Display a description for this setting
	 * @since 1.0
	 */
	public function display_description() {

		if ( !empty( $this->description ) ) {

		?>

			<p class="description"><?php echo $this->description; ?></p>

		<?php

		}
	}

	/**
	 * Generate an option input field name, using the grouped schema:
	 * "page[option_name]"
	 * @since 1.2
	 */
	public function get_input_name() {
		return esc_attr( $this->page ) . '[' . esc_attr( $this->id ) . ']';
	}

	/**
	 * Add and register this setting
	 *
	 * @since 1.0
	 */
	public function add_settings_field( $section_id ) {

		// If no sanitization callback exists, don't register the setting.
		if ( !$this->has_sanitize_callback() ) {
			return;
		}

		add_settings_field(
			$this->id,
			$this->title,
			array( $this, 'display_setting' ),
			$this->tab,
			$section_id,
			$this->args
		);

	}

	/**
	 * Check if this field has a sanitization callback set
	 * @since 1.2
	 */
	public function has_sanitize_callback() {
		if ( isset( $this->sanitize_callback ) && trim( $this->sanitize_callback ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Set an error
	 * @since 1.0
	 */
	public function set_error( $error ) {
		$this->errors[] = array_merge(
			$error,
			array(
				'class'		=> get_class( $this ),
				'id'		=> $this->id,
				'backtrace'	=> debug_backtrace()
			)
		);
	}

	/**
	 * Check if a setting has a position
	 * @since 2.0.a.9
	 */
	public function has_position() {
		return !empty( $this->position ) && is_array( $this->position ) && !empty( $this->position[0] );
	}
}
