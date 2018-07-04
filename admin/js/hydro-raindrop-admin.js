( function ( $ ) {
	"use strict";

	$( window ).load( function () {
		$( "#hydro_id_link" ).on( "click", function () {
			$.post( `/wp-json/hydro-raindrop/v1/register-user`, {
				hydro_id: $( "#hydro_id" ).val()
			} );
		} );

		$( "#hydro_id_authenticate" ).on( "click", function () {
			$.post( "/wp-json/hydro-raindrop/v1/verify-signature", {
				hydro_id: $( "#hydro_id" ).val(),
				message: "123456"
			} );
		} );
	} );

} )( jQuery );
