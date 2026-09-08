/**
 * Loaded on every wp-admin screen. Handles the dismissible plugin notices.
 *
 * The dashboard itself is a React app built from src/ - do not add dashboard
 * logic here.
 */
( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.wpb-notice .notice-dismiss', function () {
		var notice = $( this ).closest( '.wpb-notice' ).data( 'notice' );

		if ( ! notice ) {
			return;
		}

		$.post( window.ajaxurl, {
			action: 'wpb_dismiss_notice',
			notice: notice,
			nonce: window.wpbAdmin ? window.wpbAdmin.nonce : '',
		} );
	} );
} )( jQuery );
