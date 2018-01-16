<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrNotificationEmail' ) ) {
/**
 * Class to handle an email notification for Restaurant Reservations
 *
 * This class extends TREMtrNotification and must implement the following methods:
 *	prepare_notification() - set up and validate data
 *	send_notification()
 *
 * @since 0.0.1
 */
class TREMtrNotificationEmail extends TREMtrNotification {

	/**
	 * Recipient email
	 * @since 0.0.1
	 */
	public $to_email;

	/**
	 * From email
	 * @since 0.0.1
	 */
	public $from_email;

	/**
	 * From name
	 * @since 0.0.1
	 */
	public $from_name;

	/**
	 * Email subject
	 * @since 0.0.1
	 */
	public $subject;

	/**
	 * Email message body
	 * @since 0.0.1
	 */
	public $message;

	/**
	 * Email headers
	 * @since 0.0.1
	 */
	public $headers;

	/**
	 * Prepare and validate notification data
	 *
	 * @return boolean if the data is valid and ready for transport
	 * @since 0.0.1
	 */
	public function prepare_notification() {

		$this->set_to_email();
		$this->set_from_email();
		$this->set_subject();
		$this->set_headers();
		$this->set_message();

		// Return false if we're missing any of the required information
		if ( 	empty( $this->to_email) ||
				empty( $this->from_email) ||
				empty( $this->from_name) ||
				empty( $this->subject) ||
				empty( $this->headers) ||
				empty( $this->message) ) {
			return false;
		}

		return true;
	}

	/**
	 * Set to email
	 * @since 0.0.1
	 */
	public function set_to_email() {

		if ( $this->target == 'user' ) {
			$to_email = empty( $this->reservation->email ) ? null : $this->reservation->email;

		} else {
			global $tremtr_controller;
			$to_email = $tremtr_controller->settings->get_setting( 'admin-email-address' );
		}

		$this->to_email = apply_filters( 'tremtr_notification_email_to_email', $to_email, $this );

	}

	/**
	 * Set from email
	 * @since 0.0.1
	 */
	public function set_from_email() {

		global $tremtr_controller;

		if ( $this->target == 'user' ) {
			$from_email = $tremtr_controller->settings->get_setting( 'reply-to-address' );
			$from_name = $tremtr_controller->settings->get_setting( 'reply-to-name' );
		} else {
			$from_email = $this->reservation->email;
			$from_name = $this->reservation->name;
		}

		$this->from_email = apply_filters( 'tremtr_notification_email_from_email', $from_email, $this );
		$this->from_name = apply_filters( 'tremtr_notification_email_from_name', $from_name, $this );

	}

	/**
	 * Set email subject
	 * @since 0.0.1
	 */
	public function set_subject() {

		global $tremtr_controller;

		if( $this->event == 'new_submission' ) {
			if ( $this->target == 'admin' ) {
				$subject = $tremtr_controller->settings->get_setting( 'subject-reservation-admin' );
			} elseif ( $this->target == 'user' ) {
				$subject = $tremtr_controller->settings->get_setting( 'subject-reservation-user' );
			}

		} elseif ( $this->event == 'pending_to_confirmed' ) {
			$subject = $tremtr_controller->settings->get_setting( 'subject-confirmed-user' );

		} elseif ( $this->event == 'pending_to_closed' ) {
			$subject = $tremtr_controller->settings->get_setting( 'subject-rejected-user' );

		// Use a subject that's been appended manually if available
		} else {
			$subject = empty( $this->subject ) ? '' : $this->subject;
		}

		$this->subject = $this->process_subject_template( apply_filters( 'tremtr_notification_email_subject', $subject, $this ) );

	}

	/**
	 * Set email headers
	 * @since 0.0.1
	 */
	public function set_headers( $headers = null ) {

		global $tremtr_controller;

		$headers = "From: " . stripslashes_deep( html_entity_decode( $tremtr_controller->settings->get_setting( 'reply-to-name' ), ENT_COMPAT, 'UTF-8' ) ) . " <" . apply_filters( 'tremtr_notification_email_header_from_email', get_option( 'admin_email' ) ) . ">\r\n";
		$headers .= "Reply-To: =?utf-8?Q?" . quoted_printable_encode( $this->from_name ) . "?= <" . $this->from_email . ">\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";

		$this->headers = apply_filters( 'tremtr_notification_email_headers', $headers, $this );

	}

	/**
	 * Set email message body
	 * @since 0.0.1
	 */
	public function set_message() {

		if ( $this->event == 'new_submission' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-reservation-user' );
			} elseif ( $this->target == 'admin' ) {
				$template = $this->get_template( 'template-reservation-admin' );
			}

		} elseif ( $this->event == 'pending_to_confirmed' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-confirmed-user' );
			}

		} elseif ( $this->event == 'pending_to_closed' ) {
			if ( $this->target == 'user' ) {
				$template = $this->get_template( 'template-rejected-user' );
			}

		// Use a message that's been appended manually if available
		} else {
			$template = empty( $this->message ) ? '' : $this->message;
		}

		$template = apply_filters( 'tremtr_notification_email_template', $template, $this );

		if ( empty( $template ) ) {
			$this->message = '';
		} else {
			$this->message = wpautop( $this->process_template( $template ) );
		}

	}

	/**
	 * Process template tags for email subjects
	 * @since 0.0.1
	 */
	public function process_subject_template( $subject ) {

		$template_tags = array(
			'{user_name}'		=> $this->reservation->name,
			'{table}'			=> $this->reservation->table,
			'{persons}'			=> $this->reservation->persons,
			'{date}'			=> $this->reservation->format_date( $this->reservation->reservation_date )
		);

		$template_tags = apply_filters( 'tremtr_notification_email_subject_template_tags', $template_tags, $this );

		return str_replace( array_keys( $template_tags ), array_values( $template_tags ), $subject );

	}

	/**
	 * Send notification
	 * @since 0.0.1
	 */
	public function send_notification() {
		wp_mail( $this->to_email, $this->subject, $this->message, $this->headers );
	}
}
} // endif;
