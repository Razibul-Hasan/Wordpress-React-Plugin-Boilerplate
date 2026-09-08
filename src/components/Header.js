import { useNav } from '../context/NavContext';
import { classNames } from '../utils/Utils';

const TABS = [
	{ key: 'dashboard', label: 'Dashboard' },
	{ key: 'settings', label: 'Settings' },
];

const Header = () => {
	const { currentNav, setCurrentNav } = useNav();

	return (
		<div className="wpb-header">
			<div className="wpb-header__brand">WP Boilerplate</div>
			<nav className="wpb-header__nav">
				{ TABS.map( ( tab ) => (
					<button
						key={ tab.key }
						type="button"
						className={ classNames(
							'wpb-header__tab',
							currentNav === tab.key && 'is-active'
						) }
						onClick={ () => setCurrentNav( tab.key ) }
					>
						{ tab.label }
					</button>
				) ) }
			</nav>
		</div>
	);
};

export default Header;
