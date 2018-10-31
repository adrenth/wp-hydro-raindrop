$j = jQuery.noConflict();

( function ( $j ) {
	"use strict";

	$j( window ).load( function() {

		// Check if the cookie flag is enabled.
		if ( typeof HYDRO_MFA_TIMED_OUT_NOTICE !== "undefined" && HYDRO_MFA_TIMED_OUT_NOTICE ) {

			var doc = new DOMParser().parseFromString( HYDRO_MFA_TIMED_OUT, "text/html" );

			$j( "body" ).prepend( decodeURI( doc.documentElement.textContent ) );
			$j( "body" ).addClass( "hydro-mfa-timed-out-displayed" );

			$j( "#hydro-mfa-timed-out-notice .close a" ).click( function ( event ) {
				event.preventDefault();
				$j( "body" ).removeClass( "hydro-mfa-timed-out-displayed" );
				$j( "#hydro-mfa-timed-out-notice" ).remove();
			} );

			document.cookie = "hydro_raindrop_cookie_mfa_timed_out=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
		}

	} );

} )( jQuery );
