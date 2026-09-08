import { createContext, useContext, useState } from 'react';

const NavContext = createContext( null );

/**
 * Tracks which dashboard screen is active and keeps the URL hash in step,
 * so a screen can be linked to and survives a page reload.
 */
export const NavProvider = ( { children } ) => {
	const [ currentNav, setNav ] = useState(
		window.location.hash.replace( '#', '' ) || 'dashboard'
	);

	const setCurrentNav = ( nav ) => {
		setNav( nav );
		if ( window.location.hash.replace( '#', '' ) !== nav ) {
			window.location.hash = nav;
		}
	};

	return (
		<NavContext.Provider value={ { currentNav, setCurrentNav } }>
			{ children }
		</NavContext.Provider>
	);
};

export const useNav = () => useContext( NavContext );
