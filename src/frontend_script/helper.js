/**
 * Data passed from PHP via wp_localize_script(). See Initialization::frontend_scripts_callback().
 */
export const frontendData = window.wpbFrontendData || {};

/**
 * POST to a plugin REST route from the frontend.
 *
 * @param {string} endpoint Route name, e.g. 'settings'.
 * @param {Object} body     Payload to send.
 * @return {Promise<Object>} Parsed JSON response.
 */
export const post = async ( endpoint, body ) => {
	const response = await fetch( `${ frontendData.rest }${ endpoint }`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify( body ),
	} );

	return response.json();
};
