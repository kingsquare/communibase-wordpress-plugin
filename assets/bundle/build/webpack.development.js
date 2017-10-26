const webpack = require('webpack');
const path = require('path');

/* eslint-disable max-len */

module.exports = {
    entry: [
        'webpack-dev-server/client?http://communibase-wordpress-plugin.localhost.kingsquare.eu:8080/',
        'webpack/hot/only-dev-server',
        path.join(__dirname, '/../src/index.js')
    ],
    output: {
        path: path.join(__dirname, '../..'),
        publicPath: '/wp-content/plugins/communibase/assets/',
        filename: 'bundle.dev.js'
    },
    devtool: 'eval',
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        cacheDirectory: true,
                        plugins: ['transform-runtime'],
                        presets: ['es2015', 'react', 'stage-0'],
                        env: {
                            development: {
                                plugins: [
                                    ['react-transform', {
                                        transforms: [{
                                            transform: 'react-transform-hmr',
                                            imports: ['react'],
                                            locals: ['module']
                                        }, {
                                            transform: 'react-transform-catch-errors',
                                            imports: ['react', 'redbox-react']
                                        }]
                                    }]
                                ]
                            }
                        }
                    }
                }
            },
            {
                test: /\.scss$/,
                use: [
                    'style-loader',
                    'css-loader?sourceMap&-minimize&importLoaders=1&localIdentName=[path]___[name]__[local]___[hash:base64:5]',
                    'resolve-url-loader?sourceMap',
                    'sass-loader?sourceMap'
                ]
            },
            {
                test: /\.(eot|woff|woff2|ttf|svg|png|jpg|gif)$/,
                use: 'url-loader'
            },
            {
                test: /\.hbs/,
                use: 'raw-loader'
            },
            {
                test: /\.json/,
                use: 'json-loader'
            }
        ],
        loaders: [
        ]
    },
    resolve: {
        modules: [
            path.resolve('../src'),
            'node_modules'
        ]
    },
    plugins: [
        new webpack.LoaderOptionsPlugin({
            resolveUrlLoader: {
                root: path.resolve(__dirname, '../view/layout')
            }
        }),
        new webpack.ContextReplacementPlugin(/moment[/\\]locale$/, /nl/),
        new webpack.IgnorePlugin(/regenerator|nodent|js-beautify/, /ajv/),
        new webpack.DefinePlugin({
            '__DEV__': 'true',
            '__DEBUG__': 'true',
            'NODE_ENV': JSON.stringify('development'),
            'process.env.NODE_ENV': JSON.stringify('development')
        }),
        new webpack.HotModuleReplacementPlugin(),
        new webpack.NamedModulesPlugin()
    ],
    devServer: {
        hot: true,
        host: 'communibase-wordpress-plugin.localhost.kingsquare.eu',
        port: 8080,
        disableHostCheck: true, // this allows 0.0.0.0
        contentBase: path.join(__dirname, '../../'),
        proxy: {
            '/': {
                target: 'http://communibase-wordpress-plugin.localhost.kingsquare.eu',
                secure: false
            }
        }
    }
};
