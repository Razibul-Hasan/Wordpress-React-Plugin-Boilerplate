import { useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Header from './components/Header';
import { AppProvider } from './context/AppContext';
import { NavProvider, useNav } from './context/NavContext';
import Overview from './dashboard/overview';
import Settings from './dashboard/settings';

/**
 * Maps a hash route to the component that renders it.
 * Add a screen by dropping a folder in src/dashboard and registering it here.
 */
const routeConfig = {
	dashboard: { component: Overview, showHeader: true },
	settings: { component: Settings, showHeader: true },
};

const DEFAULT_ROUTE = 'dashboard';

const App = () => {
	const { currentNav, setCurrentNav } = useNav();
	const route = routeConfig[ currentNav ] || routeConfig[ DEFAULT_ROUTE ];

	// Keep the view in sync when the user edits the hash or uses the back button.
	useEffect( () => {
		const abortControl = new AbortController();

		const syncFromHash = () => {
			const hash = window.location.hash.replace( '#', '' );
			setCurrentNav( hash || DEFAULT_ROUTE );
		};

		window.addEventListener( 'hashchange', syncFromHash, {
			signal: abortControl.signal,
		} );
		syncFromHash();

		return () => abortControl.abort();
	}, [] );

	const RouteComponent = route.component;

	return (
		<AppProvider>
			<div className="wpb-app">
				{ route.showHeader && <Header /> }
				<div className="wpb-content">
					<RouteComponent />
				</div>
			</div>
		</AppProvider>
	);
};

const container = document.getElementById( 'wpb-dashboard-wrap' );

if ( container ) {
	createRoot( container ).render(
		<NavProvider>
			<App />
		</NavProvider>
	);
}
