jQuery( document ).ready( function ( $ ) {
	$( "#resetHydroId" ).click( function ( e ) {
		e.preventDefault();
		if ( window.confirm( 'Are you sure? This cannot be undone.' ) ) {
			$.post(
				hydro_raindrop_ajax.ajax_url,
				{
					'_ajax_nonce': hydro_raindrop_ajax.nonce,
					'action': "reset-hydro-id",
					'user_id': $( this ).data( 'user-id' )
				},
				function ( response ) {
					if ( response === 'OK' ) {
						window.alert( 'HydroID for this user has been reset.' );
					}
				}
			);
		}
	} );
} );
