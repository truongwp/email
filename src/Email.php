<?php
/**
 * Abstract email class for WordPress
 *
 * @package Truongwp
 * @author Truong Giang <truongwp@gmail.com>
 * @version 1.0.0
 */

namespace Truongwp;

/**
 * Class Truongwp\Email
 */
abstract class Email {

	/**
	 * Whether to use HTML in email.
	 *
	 * @var bool
	 */
	protected $html = false;

	/**
	 * Replace strings in email content.
	 *
	 * @var array
	 */
	protected $replaces = array();

	/**
	 * Prefix for hooks.
	 *
	 * @var string
	 */
	protected $prefix = 'truongwp_';

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->replaces = array(
			'%%site_name%%' => get_bloginfo( 'name' ),
		);
	}

	/**
	 * Gets email subject.
	 *
	 * @abstract
	 * @access protected
	 *
	 * @return string
	 */
	abstract protected function subject();

	/**
	 * Gets email content header.
	 *
	 * @return string
	 */
	protected function content_header() {
		return '';
	}

	/**
	 * Gets email content footer.
	 *
	 * @return string
	 */
	protected function content_footer() {
		return '';
	}

	/**
	 * Gets email content body.
	 *
	 * @abstract
	 *
	 * @return string
	 */
	abstract protected function content_body();

	/**
	 * Gets css for email content.
	 *
	 * @return string
	 */
	protected function content_css() {
		return '';
	}

	/**
	 * Gets attachments.
	 *
	 * @return array
	 */
	protected function attachments() {
		return array();
	}

	/**
	 * Gets headers.
	 *
	 * @return array
	 */
	protected function headers() {
		return array();
	}

	/**
	 * Replaces string in email content.
	 *
	 * @param string $find    String needs to be replaced.
	 * @param string $replace Replaced string.
	 */
	public function replace( $find, $replace ) {
		$this->replaces[ $find ] = $replace;
	}

	/**
	 * Sends mail.
	 *
	 * @param string $recipent Recipent email address.
	 */
	public function send( $recipent ) {
		$content = $this->get_content();

		if ( $this->html ) {
			add_filter( 'wp_mail_content_type', array( $this, 'content_type_html' ) );
		}

		/**
		 * Fires before sending mails.
		 *
		 * @since 0.1.0
		 *
		 * @param Email $email Email object.
		 */
		do_action( "{$this->prefix}email_before_sending", $this );

		wp_mail( $recipent, $this->subject(), $content, $this->headers(), $this->attachments() );

		/**
		 * Fires after sending mail.
		 *
		 * @since 0.1.0
		 *
		 * @param Email $email Email object.
		 */
		do_action( "{$this->prefix}email_after_sending", $this );

		if ( $this->html ) {
			remove_filter( 'wp_mail_content_type', array( $this, 'content_type_html' ) );
		}
	}

	/**
	 * Gets email content.
	 *
	 * @return string
	 */
	public function get_content() {
		$content = $this->content_header() . $this->content_body() . $this->content_footer();

		if ( $this->replaces ) {
			$content = str_replace( array_keys( $this->replaces ), array_values( $this->replaces ), $content );
		}

		if ( $this->html && $this->content_css() ) {
			$emogrifier = new \Pelago\Emogrifier( $content, $this->content_css() );
			$content = $emogrifier->emogrify();
		}

		return $content;
	}

	/**
	 * Filters mail content type.
	 *
	 * @return string
	 */
	public function content_type_html() {
		return 'text/html';
	}
}
