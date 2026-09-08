const path = require( 'path' );

const config = {
	mode: 'development',
	devtool: 'source-map',
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: { loader: 'babel-loader' },
			},
			{
				test: /\.(s(a|c)ss)$/,
				use: [
					'style-loader',
					'css-loader',
					{
						loader: 'sass-loader',
						options: {
							api: 'modern',
						},
					},
				],
			},
		],
	},
	plugins: [],
};

// Each entry maps a source file to the bundle enqueued from PHP.
// Add a new key here when the plugin needs another bundle.
const mainExport = Object.assign( {}, config, {
	entry: {
		'./assets/js/wpb': './src/index.js',
		'./assets/js/frontend-script': './src/frontend_script/index.js',
	},
	output: {
		path: path.join( __dirname, './' ),
		filename: '[name].js',
	},
} );

module.exports = [ mainExport ];
