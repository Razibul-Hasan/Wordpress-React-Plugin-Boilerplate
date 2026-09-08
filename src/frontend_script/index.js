import { frontendData } from './helper';

/**
 * Frontend entry point. Bundled to assets/js/frontend-script.js and enqueued
 * by Initialization::frontend_scripts_callback().
 */
const init = () => {
	const roots = document.querySelectorAll( '.wpb-frontend' );

	roots.forEach( ( root ) => {
		// Frontend behaviour goes here. `frontendData` holds the values
		// localized from PHP (rest url, nonce, ajax url).
		root.dataset.wpbReady = frontendData.rest ? 'true' : 'false';
	} );
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
