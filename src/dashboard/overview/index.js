import { backendData } from '../../utils/Utils';

const Overview = () => (
	<div className="wpb-panel">
		<h2 className="wpb-panel__title">Overview</h2>
		<p className="wpb-panel__text">
			Welcome, { backendData?.userInfo?.name || 'there' }. This screen is
			the starting point for the plugin dashboard — replace it with
			whatever the plugin should show first.
		</p>
		<p className="wpb-panel__meta">Version { backendData?.version }</p>
	</div>
);

export default Overview;
