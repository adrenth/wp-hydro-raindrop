<?php

declare( strict_types=1 );

use Adrenth\Raindrop\ApiAccessToken;
use Adrenth\Raindrop\TokenStorage\TokenStorage;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Hydro_Raindrop_TransientTokenStorage
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
final class Hydro_Raindrop_TransientTokenStorage implements TokenStorage {

	private const TRANSIENT_ID = 'HydroRaindropTokenStorage';

	/**
	 * {@inheritdoc}
	 */
	public function getAccessToken() : ?ApiAccessToken {

		$data = get_transient( self::TRANSIENT_ID );

		if ( is_string( $data ) && substr_count( $data, '|' ) === 1 ) {
			$data = explode( '|', $data );

			return ApiAccessToken::create( $data[0] ?? '', (int) ( $data[1] ?? 0 ) );
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAccessToken( ApiAccessToken $token ) : void {

		set_transient(
			self::TRANSIENT_ID,
			$token->getToken() . '|' . $token->getExpiresIn(),
			$token->getExpiresIn()
		);

	}

	/**
	 * {@inheritdoc}
	 */
	public function unsetAccessToken() : void {

		delete_transient( self::TRANSIENT_ID );

	}

}
