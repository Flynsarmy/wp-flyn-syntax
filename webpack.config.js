/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const isProduction = process.env.NODE_ENV === 'production';

const config = {
	...defaultConfig,
	entry: {
		'block': './assets/js/src/block.js',
	},
	output: {
		path: __dirname + '/assets/js/build/',
		filename: '[name].min.js',
		library: 'FlynSyntax',
		libraryTarget: 'this',
	},
	module: {
		...defaultConfig.module,
	},
	plugins: [
		...defaultConfig.plugins,
	],
};

if ( ! isProduction ) {
	config.module.rules.unshift({
		test: /\.jsx?$/,
		enforce: "pre",
		loader: "eslint-loader",
		exclude: /node_modules/,
		options: {
			emitWarning: true,
			configFile: "./.eslintrc.js"
		}
	});
}

module.exports = config;