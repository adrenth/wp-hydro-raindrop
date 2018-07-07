<?php

declare( strict_types=1 );

use Adrenth\Raindrop\ApiAccessToken;
use Adrenth\Raindrop\TokenStorage\TokenStorage;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Hydro_Raindrop_TransientTokenStorage
 */
final class Hydro_Raindrop_TransientTokenStorage implements TokenStorage {

	private const TRANSIENT_ID = 'HydroRaindropTokenStorage';

	public function getAccessToken(): ?ApiAccessToken {

		$data = get_transient( self::TRANSIENT_ID );

		if ( is_string($data) && substr_count( $data, '|' ) === 1 ) {
			$data = explode( '|', $data );
			return ApiAccessToken::create( $data[0] ?? '', (int) ($data[1] ?? 0) );
		}

		return null;
	}

	public function setAccessToken( ApiAccessToken $token ): void {
		set_transient( self::TRANSIENT_ID, $token->getToken() . '|'. $token->getExpiresIn(), $token->getExpiresIn() );
	}

	public function unsetAccessToken(): void {
		delete_transient( self::TRANSIENT_ID );
	}

}
