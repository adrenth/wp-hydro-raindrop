<?php

declare( strict_types=1 );

use Adrenth\Raindrop\ApiAccessToken;
use Adrenth\Raindrop\Exception\UnableToAcquireAccessToken;
use Adrenth\Raindrop\TokenStorage\TokenStorage;

/**
 * Class Hydro_Raindrop_TransientTokenStorage
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
final class Hydro_Raindrop_TransientTokenStorage implements TokenStorage {

	const TRANSIENT_ID = 'hydro_raindrop_token_storage';

	/**
	 * {@inheritdoc}
	 */
	public function getAccessToken() : ApiAccessToken {

		$data = get_transient( self::TRANSIENT_ID );

		if ( is_string( $data ) && substr_count( $data, '|' ) === 1 ) {
			$data = explode( '|', $data );

			return ApiAccessToken::create( $data[0] ?? '', (int) ( $data[1] ?? 0 ) );
		}

		throw new UnableToAcquireAccessToken( 'Access Token is not found in the storage.' );

	}

	/**
	 * {@inheritdoc}
	 */
	public function setAccessToken( ApiAccessToken $token ) {

		set_transient(
			self::TRANSIENT_ID,
			$token->getToken() . '|' . $token->getExpiresAt(),
			$token->getExpiresAt() - time()
		);

	}

	/**
	 * {@inheritdoc}
	 */
	public function unsetAccessToken() {

		delete_transient( self::TRANSIENT_ID );

	}

}
