const path = require('path');

const ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
	entry: './_src/js/app.js',
	output: {
		filename: 'js/app.js',
		path: path.resolve(__dirname, 'admin')
	},
	module: {
		rules: [{
			test: /\.js$/,
			exclude: /(node_modules|bower_components)/,
			use: {
				loader: 'babel-loader',
				options: {
					presets: ['@babel/preset-env']
				}
			}
		},{
			test: /\.scss$/,
			//exclude: /(node_modules|bower_components)/,
			use: ExtractTextPlugin.extract({
				use: [{
					loader: "css-loader",
					options: {
						sourceMap: true
					}
				}, {
					loader: "sass-loader",
					options: {
						sourceMap: true
					}
				}],
				fallback: "style-loader" // Use style-loader in development
			})
		}]
	},
	devtool: 'source-map', // Generates source Maps for these files
	plugins: [
		new ExtractTextPlugin({
			filename: "css/app.css",
			disable: process.env.NODE_ENV === "development"
		})
	],
	watchOptions: {
		poll: 500
	}
};