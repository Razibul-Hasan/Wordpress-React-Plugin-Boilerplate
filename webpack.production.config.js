'use strict';

const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' );

// Packages WordPress core already ships. Mapping them here keeps them out of
// the bundle and makes the plugin use the same React instance as wp.components,
// which is what stops "invalid hook call" errors. Every key added here needs the
// matching script handle in Initialization::admin_scripts_callback().
const externals = {
	react: 'React',
	'react-dom': 'ReactDOM',
	'react-dom/client': 'ReactDOM',
	'@wordpress/element': [ 'wp', 'element' ],
	'@wordpress/components': [ 'wp', 'components' ],
	'@wordpress/i18n': [ 'wp', 'i18n' ],
	'@wordpress/api-fetch': [ 'wp', 'apiFetch' ],
};

const config = {
	mode: 'production',
	externals,
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
	optimization: {
		minimize: true,
		concatenateModules: false,
		minimizer: [
			new TerserPlugin( {
				parallel: true,
				terserOptions: {
					output: {
						comments: /translators:/i,
					},
					compress: {
						passes: 2,
					},
					// Keep the i18n function names intact so WP can parse them.
					mangle: {
						reserved: [ '__', '_n', '_nx', '_x' ],
					},
				},
				extractComments: true,
			} ),
		],
	},
};

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
