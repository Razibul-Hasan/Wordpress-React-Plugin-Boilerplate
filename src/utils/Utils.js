/**
 * Data passed from PHP via wp_localize_script(). See Initialization::admin_scripts_callback().
 */
export const backendData = window.wpbBackendData || {};

/**
 * Thin wrapper over fetch that points at the plugin's REST namespace and
 * attaches the nonce WordPress needs to authenticate a logged in request.
 *
 * @param {string} endpoint Route name, e.g. 'settings'.
 * @param {Object} options  Standard fetch options.
 * @return {Promise<Object>} Parsed JSON response.
 */
export const apiRequest = async ( endpoint, options = {} ) => {
	const { rest, restNonce } = backendData;

	try {
		const response = await fetch( `${ rest }${ endpoint }`, {
			...options,
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': restNonce,
				...( options.headers || {} ),
			},
		} );

		return await response.json();
	} catch ( error ) {
		return { success: false, message: error.message };
	}
};

export const apiGet = ( endpoint ) => apiRequest( endpoint, { method: 'GET' } );

export const apiPost = ( endpoint, body ) =>
	apiRequest( endpoint, {
		method: 'POST',
		body: JSON.stringify( body ),
	} );

/**
 * Join class names, dropping anything falsy.
 *
 * @param {...(string|false|null|undefined)} classes Candidate class names.
 * @return {string} Space separated class list.
 */
export const classNames = ( ...classes ) =>
	classes.filter( Boolean ).join( ' ' );
