const path = require( 'path' );
const webpack = require( 'webpack' );
const autoprefixer = require( 'autoprefixer' );

// Plugins
const ExtractTextPlugin = require( 'mini-css-extract-plugin' );
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
	mode: 'production',

	entry: {
		'app': path.resolve( __dirname, '_src/js/index.js' ),
	},

	output: {
		filename: '[name].min.js',
		path: path.resolve( __dirname, 'admin/js' )
	},

	module: {
		rules: [{
			test: /\.(js|jsx)$/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader',
				options: {
					presets: [ '@babel/preset-env', '@babel/preset-react' ]
				}
			}
		},{
			test: /\.scss$/,
			exclude: /node_modules/,
			use: [ ExtractTextPlugin.loader,
				{
					loader: 'css-loader',
					options: {
						sourceMap: true
					}
				}, {
					loader: 'sass-loader',
					options: {
						sourceMap: true,
						plugins: [
							autoprefixer({
								browsers:['ie >= 8', 'last 3 version']
							})
						],
					}
				}
			]
		}]
	},

	// This will allow us to import files without writing these extension
	// eg: import 'app', instead of import 'app.jsx'
	resolve: {
		extensions: [".js", ".jsx", ".json"],
	},

	plugins: [
		new CleanWebpackPlugin([
			'admin/js',
			'admin/css'
		]),
		new ExtractTextPlugin({
			filename: '../css/[name].min.css'
		}),
		new webpack.DefinePlugin({
			'process.env.NODE_ENV': JSON.stringify( 'production' )
		})
	],

	devtool: 'source-map', // Generates source Maps for these files

	stats: {
		colors: true,
		entrypoints: true
	},

	watchOptions: {
		ignored: /node_modules/,
		poll: 1000
	}
};