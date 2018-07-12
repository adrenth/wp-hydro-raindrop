( function ( $ ) {
	"use strict";

	$( window ).load( function () {

		$( "#hydro_raindrop_authenticate" ).on( "click", function () {
			$.ajax( {
				method: "POST",
				url: "/wp-json/hydro-raindrop/v1/verify-signature-login"
			} ).done( function ( msg ) {
				console.log( msg );
			} );
		} );

	} );

} )( jQuery );
