$j = jQuery.noConflict();

( function ( $j ) {
	"use strict";

	$j( window ).load( function() {

		// Check if the cookie flag is enabled.
		if ( typeof HYDRO_MFA_TIMED_OUT_NOTICE !== "undefined" && HYDRO_MFA_TIMED_OUT_NOTICE ) {
			$j( "body" ).prepend( HYDRO_MFA_TIMED_OUT );
			$j( "body" ).addClass( "hydro-mfa-timed-out-displayed" );

			$j( '#hydro-mfa-timed-out-notice .close a' ).click( function ( event ) {
				event.preventDefault();
				$j( "body" ).removeClass( "hydro-mfa-timed-out-displayed" );
				$j( "#hydro-mfa-timed-out-notice" ).remove();
			} );

			var delete_cookie = function( name ) {
				document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
			};
			delete_cookie( "COOKIE_MFA_TIMED_OUT" );
		}

	} );

} )( jQuery );
