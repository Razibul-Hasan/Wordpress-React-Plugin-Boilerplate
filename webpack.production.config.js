'use strict';

const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' );

const config = {
	mode: 'production',
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
