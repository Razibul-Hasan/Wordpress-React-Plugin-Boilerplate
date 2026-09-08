import { createContext, useContext, useEffect, useState } from 'react';
import { apiGet, apiPost } from '../utils/Utils';

const AppContext = createContext( null );

/**
 * Loads the plugin settings once and shares them with every screen.
 * Replace `settings` with whatever state the plugin actually needs.
 */
export const AppProvider = ( { children } ) => {
	const [ settings, setSettings ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ saving, setSaving ] = useState( false );

	useEffect( () => {
		apiGet( 'settings' )
			.then( ( res ) => setSettings( res?.data || {} ) )
			.finally( () => setLoading( false ) );
	}, [] );

	const saveSettings = async ( values ) => {
		setSaving( true );
		try {
			const res = await apiPost( 'settings', values );
			setSettings( res?.data || {} );
			return res;
		} finally {
			setSaving( false );
		}
	};

	return (
		<AppContext.Provider
			value={ { settings, setSettings, saveSettings, loading, saving } }
		>
			{ children }
		</AppContext.Provider>
	);
};

export const useApp = () => useContext( AppContext );
