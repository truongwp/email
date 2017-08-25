An abstract class for email sending in WordPress.

## Installation

Use composer to include the library:
`composer require truongwp/email`

## Usage

```php
/**
 * Class ExampleEmail
 */
class ExampleEmail extends Truongwp\Email {

	/**
	 * Whether to use HTML in email.
	 *
	 * @var bool
	 */
	protected $html = true;

	/**
	 * Gets email subject.
	 *
	 * @return string
	 */
	protected function subject() {
		return 'Example email subject';
	}

	/**
	 * Gets email content body.
	 *
	 * @return string
	 */
	protected function content_body() {
		return '<p>Hi %%customer_name%%.<br>This is an example email from <span class="red">%%site_name%%</span></p>';
	}

	/**
	 * Gets email content footer.
	 *
	 * @return string
	 */
	protected function content_footer() {
		if ( $this->html ) {
			return '<p>Best Regards,<br>Truongwp</p>';
		}

		return "Best Regards,\nTruongwp";
	}

	/**
	 * Gets css for email content.
	 *
	 * @return string
	 */
	protected function content_css() {
		return '.red { color: red; }';
	}
}

$email = new ExampleEmail();
$email->replace( '%%customer_name%%', 'Truong' );
$email->send( 'truongwp@gmail.com' );
```
