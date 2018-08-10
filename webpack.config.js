var webpack = require( 'webpack' ),
	NODE_ENV = process.env.NODE_ENV || 'development',
	webpackConfig = {
		entry: {
			siteTemplateInfo: './blocks/site-template-info/blocks.js',
		},
		output: {
			path: __dirname,
			filename: 'dist/block.build.js',
		},
		module: {
			loaders: [
				{
					test: /.js$/,
					loader: 'babel-loader',
					exclude: /node_modules/,
				},
				{
					test: /.scss$/,
					use: [
						"style-loader",
						"css-loader",
						"sass-loader"
					]
				},
			],
		},
		plugins: [
			new webpack.DefinePlugin( {
				'process.env.NODE_ENV': JSON.stringify( NODE_ENV )
			} ),
		],
		externals: {
			lodash: 'lodash'
		}
	};

if ( 'production' === NODE_ENV ) {
	webpackConfig.plugins.push( new webpack.optimize.UglifyJsPlugin() );
}

module.exports = webpackConfig;
