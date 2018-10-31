<?php

declare( strict_types=1 );

/**
 * Class Hydro_Raindrop_MetaBox
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
class Hydro_Raindrop_MetaBox {

	/**
	 * Initialize Meta Box.
	 *
	 * @return void
	 */
	public function init() {

		add_meta_box(
			'hydro_raindrop',
			'Hydro Raindrop',
			[ $this, 'render' ],
			null,
			'side'
		);

	}

	/**
	 * Save post meta data.
	 *
	 * @param int $post_id Current Post ID.
	 * @return void
	 */
	public function save( $post_id ) {

		// @codingStandardsIgnoreLine
		$mfa_required = (bool) ( $_POST[ Hydro_Raindrop_Helper::POST_META_MFA_REQUIRED ] ?? false );

		update_post_meta( $post_id, Hydro_Raindrop_Helper::POST_META_MFA_REQUIRED, $mfa_required );

	}

	/**
	 * Render Meta Box.
	 *
	 * @param WP_Post $post Current Post.
	 * @return void
	 */
	public function render( $post ) {

		require __DIR__ . '/partials/metabox/default.php';

	}

}
