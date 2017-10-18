( function() {
	'use strict';

	( function() {
		// Cookie notice
		if ( document.cookie.indexOf( 'seenCookieNotice' ) === -1 ) {
			document.cookie = 'seenCookieNotice=yes;path=/;max-age=31536000';
			document.body.classList.add( 'show-cookie-notice' );
			document.querySelector( '.cookie-notice button' ).addEventListener( 'click', function() {
				document.body.classList.remove( 'show-cookie-notice' );
			} );
		}
	} )();

} )();