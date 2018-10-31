<?php

declare( strict_types=1 );

/**
 * Flash message class.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
final class Hydro_Raindrop_Flash {

	/**
	 * Flash message expiration duration in seconds.
	 */
	const EXPIRATION_SECONDS = 10;

	/**
	 * Flash transient ID format.
	 */
	const FLASH_TRANSIENT_ID = 'hydro_raindrop_flash_%s';

	/**
	 * A unique identifier.
	 *
	 * @var string
	 */
	private $flash_id;

	/**
	 * Constructor.
	 *
	 * @param string $flash_id A unique identifier.
	 */
	public function __construct( string $flash_id ) {

		$this->flash_id = sprintf( self::FLASH_TRANSIENT_ID, $flash_id );

	}

	/**
	 * String representation of this object.
	 *
	 * @return string
	 */
	public function __toString() {

		return $this->render();

	}

	/**
	 * Render the Flash messages.
	 *
	 * @param string $html_id HTML ID for the wrapper element.
	 *
	 * @return string
	 */
	public function render( string $html_id = 'hydro-flash' ) : string {

		$flash_messages = $this->get_messages();

		$html = '';

		foreach ( $flash_messages as $type => $messages ) {
			$html .= sprintf(
				'<div id="%s" class="hydro-flash %s">',
				esc_attr( $html_id ),
				esc_attr( $type )
			);

			foreach ( (array) $messages as $message ) {
				$html .= '<p>' . esc_html( $message ) . '</p>';
			}

			$html .= '</div>';
		}

		delete_transient( $this->flash_id );

		return $html;

	}

	/**
	 * Flash a error message.
	 *
	 * @param string $message Flash message.
	 * @return Hydro_Raindrop_Flash
	 */
	public function error( string $message ) : Hydro_Raindrop_Flash {

		return $this->add_message( $message, 'error' );

	}

	/**
	 * Flash a warning message.
	 *
	 * @param string $message Flash message.
	 * @return Hydro_Raindrop_Flash
	 */
	public function warning( string $message ) : Hydro_Raindrop_Flash {

		return $this->add_message( $message, 'warning' );

	}

	/**
	 * Flash a info message.
	 *
	 * @param string $message Flash message.
	 * @return Hydro_Raindrop_Flash
	 */
	public function info( string $message ) : Hydro_Raindrop_Flash {

		return $this->add_message( $message, 'info' );

	}

	/**
	 * Flash a success message.
	 *
	 * @param string $message Flash message.
	 * @return Hydro_Raindrop_Flash
	 */
	public function success( string $message ) : Hydro_Raindrop_Flash {

		return $this->add_message( $message, 'success' );

	}

	/**
	 * Get Flash messages from temporary storage.
	 *
	 * @return array
	 */
	private function get_messages() : array {

		$data = (string) get_transient( $this->flash_id );

		if ( '' === $data ) {
			$data = '[]';
		}

		return json_decode( $data, true );

	}

	/**
	 * Add Flash message to temporary storage.
	 *
	 * @param string $message Flash message.
	 * @param string $type Flash message type.
	 * @return Hydro_Raindrop_Flash
	 */
	private function add_message( string $message, string $type ) : Hydro_Raindrop_Flash {

		$messages = $this->get_messages();

		$messages[ $type ][] = $message;

		set_transient(
			$this->flash_id,
			wp_json_encode( $messages ),
			self::EXPIRATION_SECONDS
		);

		return $this;

	}

}
