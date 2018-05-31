<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'tremtrSettings' ) ) {
/**
 * Class to handle configurable settings for Restaurant Reservations
 *
 * @since 0.0.1
 */
class tremtrSettings {

	/**
	 * Default values for settings
	 * @since 0.0.1
	 */
	public $defaults = array();

	/**
	 * Stored values for settings
	 * @since 0.0.1
	 */
	public $settings = array();

	/**
	 * Languages supported by the pickadate library
	 */
	public $supported_i8n = array(
		'ko'	=> 'ko',
		'zh'	=> 'zh',
		'bg'	=> 'bg',
		'vn'	=> 'vn',
		'bn'	=> 'bn',
		'uk'	=> 'uk',
		'cat'	=> 'cat',
		'tr'	=> 'tr',
		'cs'	=> 'cs',
		'th'	=> 'th',
		'cy'	=> 'cy',
		'sv'	=> 'sv',
		'da'	=> 'da',
		'sr'	=> 'sr',
		'de'	=> 'de',
		'sq'	=> 'sq',
		'eo'	=> 'eo',
		'sl'	=> 'sl',
		'es'	=> 'es',
		'sk'	=> 'sk',
		'et'	=> 'et',
		'si'	=> 'si',
		'it'	=> 'it',
		'fa'	=> 'fa',
		'ru'	=> 'ru',
		'fi'	=> 'fi',
		'ro'	=> 'ro',
		'fr'	=> 'fr',
		'pt'	=> 'pt',
		'gr'	=> 'gr', // Old norwegian translation kept for backwards compatibility
		'pl'	=> 'pl',
		'he'	=> 'he',
		'pa'	=> 'pa',
		'hi'	=> 'hi',
		'no'	=> 'no',
		'hr'	=> 'hr',
		'nl'	=> 'nl',
		'hu'	=> 'hu',
		'my'	=> 'my',
		'ms'	=> 'ms',
		'mk'	=> 'mk',
		'lv'	=> 'lv',
		'lt'	=> 'lt',
	);

	public function __construct() {

		add_action( 'init', array( $this, 'set_defaults' ) );

		add_action( 'init', array( $this, 'load_settings_panel' ) );

		// Order schedule exceptions and remove past exceptions
		add_filter( 'sanitize_option_tremtr-settings', array( $this, 'clean_schedule_exceptions' ), 100 );

	}

	/**
	 * Load the plugin's default settings
	 * @since 0.0.1
	 */
	public function set_defaults() {

		$this->defaults = array(

			'success-message'				=> _x( 'Thanks for your reservation!', 'tremtr' ),
			'date-format'					=> _x( 'mmmm, d', 'Default date format for display. Must match formatting rules at http://amsul.ca/pickadate.js/date/#formats', 'tremtr' ),
			'time-format'					=> _x( 'H:i', 'Default time format for display. Must match formatting rules at http://amsul.ca/pickadate.js/time/#formats', 'tremtr' ),
			'time-interval'					=> _x( '30', 'Default interval in minutes when selecting a time.', 'tremtr' ),

			'main-color'                    => '#c0ffee',
			'available-color'               => '#33cc33',
			'reserved-color'                => '#733c3c',

			// Email address where admin notifications should be sent
			'admin-email-address'			=> get_option( 'admin_email' ),

			// Name and email address which should appear in the Reply-To section of notification emails
			'reply-to-name'					=> get_bloginfo( 'name' ),
			'reply-to-address'				=> get_option( 'admin_email' ),

			// Email template sent to an admin when a new reservation request is made
			'subject-reservation-admin'			=> _x( 'New Reservation Request', 'Default email subject for admin notifications of new reservations', 'tremtr' ),
			'template-reservation-admin'		=> _x( 'A new reservation request has been made at {site_name}:

{user_name}
Table N{table} for {persons} Persons
{date}

{reservations_link}

&nbsp;

<em>This message was sent by {site_link} on {current_time}.</em>',
				'Default email sent to the admin when a new reservation request is made. The tags in {brackets} will be replaced by the appropriate content and should be left in place. HTML is allowed, but be aware that many email clients do not handle HTML very well.',
				'tremtr'
			),

			// Email template sent to a user when a new reservation request is made
			'subject-reservation-user'			=> sprintf( _x( 'Your reservation at %s is confirmed', 'Default email subject sent to user when they request a reservation. %s will be replaced by the website name', 'tremtr' ), get_bloginfo( 'name' ) ),
			'template-reservation-user'			=> _x( 'Hi {user_name},

Your reservation request has been <strong>confirmed</strong>. We look forward to seeing you soon.

<strong>Your reservation:</strong>
{user_name}
Table N{table} for {persons} Persons
{date}

&nbsp;

<em>This message was sent by {site_link} on {current_time}.</em>',
				'Default email sent to users when they make a new reservation request. The tags in {brackets} will be replaced by the appropriate content and should be left in place. HTML is allowed, but be aware that many email clients do not handle HTML very well.',
				'tremtr'
			),


			// Email template sent to a user when a reservation request is rejected
			'subject-rejected-user'			=> sprintf( _x( 'Your reservation at %s was not accepted', 'Default email subject sent to user when their reservation is rejected. %s will be replaced by the website name', 'tremtr' ), get_bloginfo( 'name' ) ),
			'template-rejected-user'		=> _x( 'Hi {user_name},

Sorry, we could not accomodate your reservation request. We\'re full or not open at the time you requested:

{user_name}
Table N{table} for {persons} Persons
{date}

&nbsp;

<em>This message was sent by {site_link} on {current_time}.</em>',
				'Default email sent to users when they make a new reservation request. The tags in {brackets} will be replaced by the appropriate content and should be left in place. HTML is allowed, but be aware that many email clients do not handle HTML very well.',
				'tremtr'
			),

			// Email sent to a user with a custom update notice from the admin
			'subject-admin-notice'			=> sprintf( _x( 'Update regarding your reservation at %s', 'Default email subject sent to users when the admin sends a custom notice email from the reservations panel.', 'tremtr' ), get_bloginfo( 'name' ) ),
		);

		$i8n = str_replace( '-', '_', get_bloginfo( 'language' ) );
		if ( array_key_exists( $i8n, $this->supported_i8n ) ) {
			$this->defaults['i8n'] = $i8n;
		}

		$this->defaults = apply_filters( 'tremtr_defaults', $this->defaults );
	}

	/**
	 * Get a setting's value or fallback to a default if one exists
	 * @since 0.0.1
	 */
	public function get_setting( $setting ) {

		if ( empty( $this->settings ) ) {
			$this->settings = get_option( 'tremtr-settings' );
		}

		if ( !empty( $this->settings[ $setting ] ) ) {
			return apply_filters( 'tremtr-setting-' . $setting, $this->settings[ $setting ] );
		}

		if ( !empty( $this->defaults[ $setting ] ) ) {
			return apply_filters( 'tremtr-setting-' . $setting, $this->defaults[ $setting ] );
		}

		return apply_filters( 'tremtr-setting-' . $setting, null );
	}

	/**
	 * Load the admin settings page
	 * @since 0.0.1
	 * @sa 
	 */
	public function load_settings_panel() {

		require_once( TREMTR_PLUGIN_DIR . '/includes/simple-admin-pages/simple-admin-pages.php' );
		$sap = sap_initialize_library(
			$args = array(
				'version'       => '2.1.0',
				'lib_url'       => TREMTR_PLUGIN_URL . '/includes/simple-admin-pages/',
			)
		);

		
		$sap->add_page(
			'submenu',
			array(
				'id'            => 'tremtr-settings',
				'title'         => sprintf( __('<a href="https://true-emotions.studio" style="display: flex;justify-content: center;margin: auto;width: 150px;"><img  src="%s/assets/images/tremlogoblack.png"></a>Settings', 'tremtr' ), TREMTR_PLUGIN_URL),
				'menu_title'    => __( 'Settings', 'tremtr' ),
				'parent_menu'	=> 'edit.php?post_type=trem-cafes',
				'description'   => '',
				'capability'    => 'manage_options',
				'default_tab'   => 'tremtr-general',
			)
		);


		$sap->add_section(
			'tremtr-settings',
			array(
				'id'            => 'tremtr-general',
				'title'         => __( 'General', 'tremtr' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'textarea',
			array(
				'id'			=> 'success-message',
				'title'			=> __( 'Success Message', 'tremtr' ),
				'description'	=> __( 'Enter the message to display when a reservation request is made.', 'tremtr' ),
				'placeholder'	=> $this->defaults['success-message'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'textarea',
			array(
				'id'			=> 'privacy-message',
				'title'			=> __( 'Privacy Message', 'tremtr' ),
				'description'	=> __( 'Enter the message to display before a reservation request is made. Leave the field blank if no message!', 'tremtr' ),
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'text',
			array(
				'id'            => 'date-format',
				'title'         => __( 'Date Format', 'tremtr' ),
				'description'   => sprintf( __( 'Define how the date is formatted on the reservation form. %sFormatting rules%s. Be very careful, incorrectly set formats can break the plugin! This only changes the format on the reservation form. To change the date format in notification messages, modify your general %sWordPress Settings%s.', 'tremtr' ), '<a href="http://amsul.ca/pickadate.js/date/#formats">', '</a>', '<a href="' . admin_url( 'options-general.php' ) . '">', '</a>' ),
				'placeholder'	=> $this->defaults['date-format'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'text',
			array(
				'id'            => 'time-format',
				'title'         => __( 'Time Format', 'tremtr' ),
				'description'   => sprintf( __( 'Define how the time is formatted on the reservation form. %sFormatting rules%s. Be very careful, incorrectly set formats can break the plugin! This only changes the format on the reservation form. To change the time format in notification messages, modify your general %sWordPress Settings%s.', 'tremtr' ), '<a href="http://amsul.ca/pickadate.js/time/#formats">', '</a>', '<a href="' . admin_url( 'options-general.php' ) . '">', '</a>' ),
				'placeholder'	=> $this->defaults['time-format'],
			)
		);

		// Add i8n setting for pickadate if the frontend assets are to be loaded
		if ( apply_filters( 'tremtr-load-frontend-assets', true ) ) {
			$sap->add_setting(
				'tremtr-settings',
				'tremtr-general',
				'select',
				array(
					'id'            => 'i8n',
					'title'         => __( 'Language', 'tremtr' ),
					'description'   => __( 'Select a language to use for the reservation form flatpickr if it is different than your WordPress language setting.', 'tremtr' ),
					'options'		=> $this->supported_i8n,
				)
			);
		}

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'color',
			array(
				'id'            => 'main-color',
				'title'         => __( 'Main Color', 'tremtr' ),
				'default_value'	=> $this->defaults['main-color'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'color',
			array(
				'id'            => 'available-color',
				'title'         => __( 'Available Table Color', 'tremtr' ),
				'default_value'	=> $this->defaults['available-color'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-general',
			'color',
			array(
				'id'            => 'reserved-color',
				'title'         => __( 'Reserved Table Color', 'tremtr' ),
				'default_value'	=> $this->defaults['reserved-color'],
			)
		);


		$sap->add_section(
			'tremtr-settings',
			array(
				'id'            => 'tremtr-schedule',
				'title'         => __( 'Reservation Schedule', 'tremtr' ),
				'is_tab'		=> true,
			)
		);

		// Translateable strings for scheduler components
		$scheduler_strings = array(
			'add_rule'			=> __( 'Add new scheduling rule', 'tremtr' ),
			'weekly'			=> _x( 'Weekly', 'Format of a scheduling rule', 'tremtr' ),
			'monthly'			=> _x( 'Monthly', 'Format of a scheduling rule', 'tremtr' ),
			'date'				=> _x( 'Date', 'Format of a scheduling rule', 'tremtr' ),
			'weekdays'			=> _x( 'Days of the week', 'Label for selecting days of the week in a scheduling rule', 'tremtr' ),
			'month_weeks'		=> _x( 'Weeks of the month', 'Label for selecting weeks of the month in a scheduling rule', 'tremtr' ),
			'date_label'		=> _x( 'Date', 'Label to select a date for a scheduling rule', 'tremtr' ),
			'time_label'		=> _x( 'Time', 'Label to select a time slot for a scheduling rule', 'tremtr' ),
			'allday'			=> _x( 'All day', 'Label to set a scheduling rule to last all day', 'tremtr' ),
			'start'				=> _x( 'Start', 'Label for the starting time of a scheduling rule', 'tremtr' ),
			'end'				=> _x( 'End', 'Label for the ending time of a scheduling rule', 'tremtr' ),
			'set_time_prompt'	=> _x( 'All day long. Want to %sset a time slot%s?', 'Prompt displayed when a scheduling rule is set without any time restrictions', 'tremtr' ),
			'toggle'			=> _x( 'Open and close this rule', 'Toggle a scheduling rule open and closed', 'tremtr' ),
			'delete'			=> _x( 'Delete rule', 'Delete a scheduling rule', 'tremtr' ),
			'delete_schedule'	=> __( 'Delete scheduling rule', 'tremtr' ),
			'never'				=> _x( 'Never', 'Brief default description of a scheduling rule when no weekdays or weeks are included in the rule', 'tremtr' ),
			'weekly_always'	=> _x( 'Every day', 'Brief default description of a scheduling rule when all the weekdays/weeks are included in the rule', 'tremtr' ),
			'monthly_weekdays'	=> _x( '%s on the %s week of the month', 'Brief default description of a scheduling rule when some weekdays are included on only some weeks of the month. %s should be left alone and will be replaced by a comma-separated list of days and weeks in the following format: M, T, W on the first, second week of the month', 'tremtr' ),
			'monthly_weeks'		=> _x( '%s week of the month', 'Brief default description of a scheduling rule when some weeks of the month are included but all or no weekdays are selected. %s should be left alone and will be replaced by a comma-separated list of weeks in the following format: First, second week of the month', 'tremtr' ),
			'all_day'			=> _x( 'All day', 'Brief default description of a scheduling rule when no times are set', 'tremtr' ),
			'before'			=> _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'tremtr' ),
			'after'				=> _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'tremtr' ),
			'separator'			=> _x( '&mdash;', 'Separator between times of a scheduling rule', 'tremtr' ),
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'scheduler',
			array(
				'id'			=> 'schedule-open',
				'title'			=> __( 'Schedule', 'tremtr' ),
				'description'	=> __( 'Define the weekly schedule during which you accept reservations.', 'tremtr' ),
				'weekdays'		=> array(
					'monday'		=> _x( 'Mo', 'Monday abbreviation', 'tremtr' ),
					'tuesday'		=> _x( 'Tu', 'Tuesday abbreviation', 'tremtr' ),
					'wednesday'		=> _x( 'We', 'Wednesday abbreviation', 'tremtr' ),
					'thursday'		=> _x( 'Th', 'Thursday abbreviation', 'tremtr' ),
					'friday'		=> _x( 'Fr', 'Friday abbreviation', 'tremtr' ),
					'saturday'		=> _x( 'Sa', 'Saturday abbreviation', 'tremtr' ),
					'sunday'		=> _x( 'Su', 'Sunday abbreviation', 'tremtr' )
				),
				'time_format'	=> $this->get_setting( 'time-format' ),
				'date_format'	=> $this->get_setting( 'date-format' ),
				'disable_weeks'	=> true,
				'disable_date'	=> true,
				'disable_all_day'=> true,
				'strings' => $scheduler_strings,
			)
		);

		$scheduler_strings['all_day'] = _x( 'Closed all day', 'Brief default description of a scheduling exception when no times are set', 'tremtr' );
		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'scheduler',
			array(
				'id'				=> 'schedule-closed',
				'title'				=> __( 'Exceptions', 'tremtr' ),
				'description'		=> __( "Define special opening hours for holidays, events or other needs. Leave the time empty if you're closed all day.", 'tremtr' ),
				'time_format'		=> $this->get_setting( 'time-format' ),
				'date_format'		=> $this->get_setting( 'date-format' ),
				'disable_weekdays'	=> true,
				'disable_weeks'		=> true,
				'strings' => $scheduler_strings,
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'select',
			array(
				'id'            => 'early-reservations',
				'title'         => __( 'Early Reservations', 'tremtr' ),
				'description'   => __( 'Select how early customers can make their reservation. (Administrators and Reservation Managers are not restricted by this setting.)', 'tremtr' ),
				'blank_option'	=> false,
				'options'       => array(
					'1' 	=> __( 'From 1 day in advance', 'tremtr' ),
					'7' 	=> __( 'From 1 week in advance', 'tremtr' ),
					'14' 	=> __( 'From 2 weeks in advance', 'tremtr' ),
					'30' 	=> __( 'From 30 days in advance', 'tremtr' )
				)
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'select',
			array(
				'id'            => 'late-reservations',
				'title'         => __( 'Late Reservations', 'tremtr' ),
				'description'   => __( 'Select how late customers can make their reservation. (Administrators and Reservation Managers are not restricted by this setting.)', 'tremtr' ),
				'blank_option'	=> false,
				'options'       => array(
					'0' 	   => __( 'Up to the last minute', 'tremtr' ),
					'15'       => __( 'At least 15 minutes in advance', 'tremtr' ),
					'30'       => __( 'At least 30 minutes in advance', 'tremtr' ),
					'45'       => __( 'At least 45 minutes in advance', 'tremtr' ),
					'60'       => __( 'At least 1 hour in advance', 'tremtr' ),
					'240'      => __( 'At least 4 hours in advance', 'tremtr' ),
				)
			)
		);


		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'select',
			array(
				'id'			=> 'time-interval',
				'title'			=> __( 'Time Interval', 'tremtr' ),
				'description'	=> __( 'Select the number of minutes between each available time.', 'tremtr' ),
				'blank_option'	=> false,
				'options'       => array(
					'30' 		=> __( 'Every 30 minutes', 'tremtr' ),
					'15' 		=> __( 'Every 15 minutes', 'tremtr' ),
					'60' 		=> __( 'Every hour', 'tremtr' ),
					'90' 		=> __( 'Every hour and 30 minutes', 'tremtr' ),
					'120' 		=> __( 'Every 2 hours', 'tremtr' ),
				)
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-schedule',
			'select',
			array(
				'id'			=> 'reservation-duration',
				'title'			=> __( 'Average reservation duration', 'tremtr' ),
				'description'	=> __( 'Select the duration of one reservation (i.e how long you clients will stay by you).', 'tremtr' ),
				'blank_option'	=> false,
				'options'       => array(
					'30' 		=> __( '30 minutes', 'tremtr' ),
					'60' 		=> __( '1 Hour', 'tremtr' ),
					'90' 		=> __( '1 Hour and 30 minutes', 'tremtr' ),
					'120' 		=> __( '2 Hour', 'tremtr' ),
					'180' 		=> __( '3 Hour', 'tremtr' ),
					'240' 		=> __( '4 Hour', 'tremtr' ),
				)
			)
		);

		$sap->add_section(
			'tremtr-settings',
			array(
				'id'            => 'tremtr-notifications',
				'title'         => __( 'Notifications', 'tremtr' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications',
			'text',
			array(
				'id'			=> 'reply-to-name',
				'title'			=> __( 'Reply-To Name', 'tremtr' ),
				'description'	=> __( 'The name which should appear in the Reply-To field of a user notification email', 'tremtr' ),
				'placeholder'	=> $this->defaults['reply-to-name'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications',
			'text',
			array(
				'id'			=> 'reply-to-address',
				'title'			=> __( 'Reply-To Email Address', 'tremtr' ),
				'description'	=> __( 'The email address which should appear in the Reply-To field of a user notification email.', 'tremtr' ),
				'placeholder'	=> $this->defaults['reply-to-address'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications',
			'toggle',
			array(
				'id'			=> 'admin-email-option',
				'title'			=> __( 'Admin Notification', 'tremtr' ),
				'label'			=> __( 'Send an email notification to an administrator when a new reservation is requested.', 'tremtr' )
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications',
			'text',
			array(
				'id'			=> 'admin-email-address',
				'title'			=> __( 'Admin Email Address', 'tremtr' ),
				'description'	=> __( 'The email address where admin notifications should be sent.', 'tremtr' ),
				'placeholder'	=> $this->defaults['admin-email-address'],
			)
		);

		$sap->add_section(
			'tremtr-settings',
			array(
				'id'            => 'tremtr-notifications-templates',
				'title'         => __( 'Email Templates', 'tremtr' ),
				'tab'			=> 'tremtr-notifications',
				'description'	=> __( 'Adjust the messages that are emailed to users and admins if reservation confirmed.', 'tremtr' ),
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications-templates',
			'html',
			array(
				'id'			=> 'template-tags-description',
				'title'			=> __( 'Template Tags', 'tremtr' ),
				'html'			=> '
					<p class="description">' . __( 'Use the following tags to automatically add reservation information to the emails. Tags labeled with an asterisk (*) can be used in the email subject as well.', 'tremtr' ) . '</p>' .
					$this->render_template_tag_descriptions(),
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications-templates',
			'text',
			array(
				'id'			=> 'subject-reservation-admin',
				'title'			=> __( 'Admin Notification Subject', 'tremtr' ),
				'description'	=> __( 'The email subject for admin notifications.', 'tremtr' ),
				'placeholder'	=> $this->defaults['subject-reservation-admin'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications-templates',
			'editor',
			array(
				'id'			=> 'template-reservation-admin',
				'title'			=> __( 'Admin Notification Email', 'tremtr' ),
				'description'	=> __( 'Enter the email an admin should receive when an initial reservation request is made.', 'tremtr' ),
				'default'		=> $this->defaults['template-reservation-admin'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications-templates',
			'text',
			array(
				'id'			=> 'subject-reservation-user',
				'title'			=> __( 'New Request Email Subject', 'tremtr' ),
				'description'	=> __( 'The email subject a user should receive when they make an initial reservation request.', 'tremtr' ),
				'placeholder'	=> $this->defaults['subject-reservation-user'],
			)
		);

		$sap->add_setting(
			'tremtr-settings',
			'tremtr-notifications-templates',
			'editor',
			array(
				'id'			=> 'template-reservation-user',
				'title'			=> __( 'New Request Email', 'tremtr' ),
				'description'	=> __( 'Enter the email a user should receive when they make an initial reservation request.', 'tremtr' ),
				'default'		=> $this->defaults['template-reservation-user'],
			)
		);

		$sap = apply_filters( 'tremtr_settings_page', $sap );

		$sap->add_admin_menus();

	}


	/**
	 * Retrieve form fields
	 *
	 * @param $request tremtrReservation Details of a reservation request made
	 * @param $args array Associative array of arguments to pass to the field:
	 *  `location` int Location post id
	 * @since 1.3
	 */
	public function get_reservation_form_fields( $request = null, $args = array() ) {

		// $request will represent a tremtrReservation object with the request
		// details when the form is being printed and $_POST data exists
		// to populate the request. All other times $request will just
		// be an empty object
		if ( $request === null ) {
			global $tremtr_controller;
			$request = $tremtr_controller->request;
		}

		/**
		 * This array defines the field details and a callback function to
		 * render each field. To customize the form output, modify the
		 * callback functions to point to your custom function. Don't forget
		 * to output an error message in your custom callback function. You
		 * can use tremtr_print_form_error( $slug ) to do this.
		 *
		 * In addition to the parameters described below, each fieldset
		 * and field can accept a `classes` array in the callback args since
		 * v1.3. These classes will be appended to the <fieldset> and
		 * <div> elements for each field. A fieldset can also take a
		 * `legend_classes` array in the callback_args which will be
		 * added to the legend element.
		 *
		 * Example:
		 *
		 * 	$fields = array(
		 * 		'fieldset'	=> array(
		 * 			'legend'	=> __( 'My Legend', 'tremtr' ),
		 * 			'callback_args'	=> array(
		 * 				'classes'		=> array( 'fieldset-class', 'other-fieldset-class' ),
		 * 				'legend_classes	=> array( 'legend-class' ),
		 *			),
		 * 			'fields'	=> array(
		 * 				'my-field'	=> array(
		 * 					...
		 * 					'callback_args'	=> array(
		 * 						'classes'	=> array( 'field-class' ),
		 *					)
		 * 				)
		 * 			)
		 * 		)
		 * 	);
		 *
		 * See /includes/template-functions.php
		 */
		$fields = array(

			// Reservation details fieldset
			'reservation'	=> array(
				'legend'	=> __( 'Book a table', 'tremtr' ),
				'fields'	=> array(
					'date'		=> array(
						'title'			=> __( 'Date', 'tremtr' ),
						'request_input'	=> empty( $request->request_date ) ? '' : $request->request_date,
						'callback'		=> 'tremtr_print_form_text_field',
						'required'		=> true,
					),
					'time'		=> array(
						'title'			=> __( 'Time', 'tremtr' ),
						'request_input'	=> empty( $request->request_time ) ? '' : $request->request_time,
						'callback'		=> 'tremtr_print_form_text_field',
						'required'		=> true,
					),
					'party'		=> array(
						'title'			=> __( 'Party', 'tremtr' ),
						'request_input'	=> empty( $request->party ) ? '' : $request->party,
						'callback'		=> 'tremtr_print_form_select_field',
						'callback_args'	=> array(
							'options'	=> $this->get_form_party_options(),
						),
						'required'		=> true,
					),
				),
			),

			// Contact details fieldset
			'contact'	=> array(
				'legend'	=> __( 'Contact Details', 'tremtr' ),
				'fields'	=> array(
					'name'		=> array(
						'title'			=> __( 'Name', 'tremtr' ),
						'request_input'	=> empty( $request->name ) ? '' : $request->name,
						'callback'		=> 'tremtr_print_form_text_field',
						'required'		=> true,
					),
					'email'		=> array(
						'title'			=> __( 'Email', 'tremtr' ),
						'request_input'	=> empty( $request->email ) ? '' : $request->email,
						'callback'		=> 'tremtr_print_form_text_field',
						'callback_args'	=> array(
							'input_type'	=> 'email',
						),
						'required'		=> true,
					),
					'phone'		=> array(
						'title'			=> __( 'Phone', 'tremtr' ),
						'request_input'	=> empty( $request->phone ) ? '' : $request->phone,
						'callback'		=> 'tremtr_print_form_text_field',
						'callback_args'	=> array(
							'input_type'	=> 'tel',
						),
					),
					'add-message'	=> array(
						'title'		=> __( 'Add a Message', 'tremtr' ),
						'request_input'	=> '',
						'callback'	=> 'tremtr_print_form_message_link',
					),
					'message'		=> array(
						'title'			=> __( 'Message', 'tremtr' ),
						'request_input'	=> empty( $request->message ) ? '' : $request->message,
						'callback'		=> 'tremtr_print_form_textarea_field',
					),
				),
			),
		);

		return apply_filters( 'tremtr_reservation_form_fields', $fields, $request, $args );
	}

	/**
	 * Get required fields
	 *
	 * Filters the fields array to return just those marked required
	 * @since 1.3
	 */
	public function get_required_fields() {

		$required_fields = array();

		$fieldsets = $this->get_reservation_form_fields();
		foreach ( $fieldsets as $fieldset ) {
			$required_fields = array_merge( $required_fields, array_filter( $fieldset['fields'], array( $this, 'is_field_required' ) ) );
		}

		return $required_fields;
	}

	/**
	 * Check if a field is required
	 *
	 * @since 1.3
	 */
	public function is_field_required( $field ) {
		return !empty( $field['required'] );
	}

	/**
	 * Render HTML code of descriptions for the template tags
	 * @since 1.2.3
	 */
	public function render_template_tag_descriptions() {

		$descriptions = apply_filters( 'tremtr_notification_template_tag_descriptions', array(
				'{user_email}'		=> __( 'Email of the user who made the reservation', 'tremtr' ),
				'{user_name}'		=> __( '* Name of the user who made the reservation', 'tremtr' ),
				'{table}'			=> __( '* Table number', 'tremtr' ),
				'{persons}'			=> __( '* Persons', 'tremtr' ),
				'{date}'			=> __( '* Date and time of the reservation', 'tremtr' ),
				'{phone}'			=> __( 'Phone number if supplied with the request', 'tremtr' ),
				'{message}'			=> __( 'Message added to the request', 'tremtr' ),
				'{site_name}'		=> __( 'The name of this website', 'tremtr' ),
				'{site_link}'		=> __( 'A link to this website', 'tremtr' ),
				'{current_time}'	=> __( 'Current date and time', 'tremtr' ),
			)
		);

		$output = '';

		foreach ( $descriptions as $tag => $description ) {
			$output .= '
				<div class="tremtr-template-tags-box">
					<strong>' . $tag . '</strong> ' . $description . '
				</div>';
		}

		return $output;
	}

	/**
	 * Sort the schedule exceptions and remove past exceptions before saving
	 *
	 * @since 1.4.6
	 */
	public function clean_schedule_exceptions( $val ) {

		if ( empty( $val['schedule-closed'] ) ) {
			return $val;
		}

		// Sort by date
		$schedule_closed = $val['schedule-closed'];
		usort( $schedule_closed, array( $this, 'sort_by_date' ) );

		// Remove exceptions more than a week old
		$week_ago = time() - 604800;
		for( $i = 0; $i < count( $schedule_closed ); $i++ ) {
			if ( strtotime( $schedule_closed[$i]['date'] ) > $week_ago ) {
				break;
			}
		}
		if ( $i ) {
			$schedule_closed = array_slice( $schedule_closed, $i );
		}

		$val['schedule-closed'] = $schedule_closed;

		return $val;
	}

	/**
	 * Sort an associative array by the value's date parameter
	 *
	 * @usedby self::clean_schedule_exceptions()
	 * @since 0.1
	 */
	public function sort_by_date( $a, $b ) {

		$ad = empty( $a['date'] ) ? 0 : strtotime( $a['date'] );
		$bd = empty( $b['date'] ) ? 0 : strtotime( $b['date'] );

		return $ad - $bd;
	}

}
} // endif;
