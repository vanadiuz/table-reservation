<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrNotification' ) ) {
/**
 * Base class to handle a notification for Restaurant Reservations
 *
 * This class sets up the notification content and sends it when run by
 * TREMtrNotifications. This class should be extended for each type of
 * notification. So, there would be a TREMtrNotificationEmail class or a
 * TREMtrNotificationSMS class.
 *
 * @since 0.0.1
 */
abstract class TREMtrNotification {

	/**
	 * Event which should trigger this notification
	 * @since 0.0.1
	 */
	public $event;

	/**
	 * Target of the notification (who/what will receive it)
	 * @since 0.0.1
	 */
	public $target;

	/**
	 * Define the notification essentials
	 * @since 0.0.1
	 */
	public function __construct( $event, $target ) {

		$this->event = $event;
		$this->target = $target;

	}

	/**
	 * Set reservation data passed from TREMtrNotifications
	 *
	 * @var object $reservation
	 * @since 0.0.1
	 */
	public function set_reservation( $reservation ) {
		$this->reservation = $reservation;
	}

	/**
	 * Prepare and validate notification data
	 *
	 * @return boolean if the data is valid and ready for transport
	 * @since 0.0.1
	 */
	abstract public function prepare_notification();

	/**
	 * Retrieve a notification template
	 * @since 0.0.1
	 */
	public function get_template( $type ) {

		global $tremtr_controller;

		$template = $tremtr_controller->settings->get_setting( $type );

		if ( $template === null ) {
			return '';
		} else {
			return $template;
		}
	}

	/**
	 * Process a template and insert reservation details
	 * @since 0.0.1
	 */
	public function process_template( $message ) {

		$template_tags = array(
			'{user_email}'		=> $this->reservation->email,
			'{user_name}'		=> $this->reservation->name,
			'{table}'			=> $this->reservation->table,
			'{persons}'			=> $this->reservation->persons,
			'{date}'			=> $this->reservation->format_date( $this->reservation->reservation_date ).__( ' From ', 'tremtr' ).$this->reservation->format_time( $this->reservation->reservation_time_begin ).__( ' to ', 'tremtr' ).$this->reservation->format_time( $this->reservation->reservation_time_end ),
			'{phone}'			=> $this->reservation->phone,
			'{message}'			=> $this->reservation->message,
			'{reservations_link}'	=> '<a href="' . admin_url( 'admin.php?page=tremtr-reservations&status=pending' ) . '">' . __( 'View pending reservations', 'tremtr' ) . '</a>',
			'{close_link}'		=> '<a href="' . admin_url( 'admin.php?page=tremtr-reservations&tremtr-quicklink=close&reservation=' . esc_attr( $this->reservation->ID ) ) . '">' . __( 'Reject this reservation', 'tremtr' ) . '</a>',
			'{site_name}'		=> get_bloginfo( 'name' ),
			'{site_link}'		=> '<a href="' . home_url( '/' ) . '">' . get_bloginfo( 'name' ) . '</a>',
			'{current_time}'	=> date( get_option( 'date_format' ), current_time( 'timestamp' ) ) . ' ' . date( get_option( 'time_format' ), current_time( 'timestamp' ) ),
		);

		$template_tags = apply_filters( 'tremtr_notification_template_tags', $template_tags, $this );

		return str_replace( array_keys( $template_tags ), array_values( $template_tags ), $message );

	}

	/**
	 * Send notification
	 * @since 0.0.1
	 */
	abstract public function send_notification();

}
} // endif;
