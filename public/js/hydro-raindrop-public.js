$j = jQuery.noConflict();

( function ( $j ) {
	"use strict";

	$j( window ).load( function () {

		// check if the cookie flag is enabled
		if (typeof hydro_mfa_timed_out_notice !== 'undefined' && hydro_mfa_timed_out_notice) {
			$j("body").prepend(hydro_mfa_timed_out);
			$j("body").addClass('hydro-mfa-timed-out-displayed');
			
			$j('#hydro-mfa-timed-out-notice .close a').click(function(event){
			  event.preventDefault();
			  $j("body").removeClass('hydro-mfa-timed-out-displayed');
			  $j("#hydro-mfa-timed-out-notice").remove();
			});
			
			var delete_cookie = function(name) {
				document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			};
			delete_cookie('COOKIE_MFA_TIMED_OUT');
		}

	} );

} )( jQuery );
