import { useState } from 'react';
import Button from '../../components/Button';
import { useApp } from '../../context/AppContext';

/**
 * Example screen showing the full round trip: read settings from the REST API,
 * edit them, write them back.
 */
const Settings = () => {
	const { settings, saveSettings, loading, saving } = useApp();
	const [ notice, setNotice ] = useState( '' );

	const [ form, setForm ] = useState( null );
	const values = form ?? settings;

	const update = ( key, value ) => setForm( { ...values, [ key ]: value } );

	const onSave = async () => {
		const res = await saveSettings( values );
		setNotice( res?.message || '' );
		setForm( null );
	};

	if ( loading ) {
		return <div className="wpb-panel">Loading settings…</div>;
	}

	return (
		<div className="wpb-panel">
			<h2 className="wpb-panel__title">Settings</h2>

			<label className="wpb-field" htmlFor="wpb-enabled">
				<input
					id="wpb-enabled"
					type="checkbox"
					checked={ !! values.enabled }
					onChange={ ( e ) => update( 'enabled', e.target.checked ) }
				/>
				<span>Enable the plugin</span>
			</label>

			<label className="wpb-field" htmlFor="wpb-example">
				<span>Example value</span>
				<input
					id="wpb-example"
					type="text"
					value={ values.example || '' }
					onChange={ ( e ) => update( 'example', e.target.value ) }
				/>
			</label>

			<Button onClick={ onSave } disabled={ saving }>
				{ saving ? 'Saving…' : 'Save changes' }
			</Button>

			{ notice && <p className="wpb-panel__meta">{ notice }</p> }
		</div>
	);
};

export default Settings;
