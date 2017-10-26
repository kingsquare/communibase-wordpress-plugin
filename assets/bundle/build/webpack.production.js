/* eslint-disable max-len */
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const fs = require('fs');

const extractCSS = new ExtractTextPlugin({
    filename: 'bundle.min.css',
    allChunks: true
});

module.exports = {
    entry: [
        path.join(__dirname, '/../src/index.js')
    ],
    output: {
        path: path.join(__dirname, '../..'),
        publicPath: '/wp-content/plugins/communibase/assets/',
        filename: 'bundle.min.js'
    },
    devtool: 'source-map',
    resolve: {
        modules: [
            path.resolve('../src'),
            'node_modules'
        ]
    },
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
                            production: {
                                plugins: [
                                    'transform-react-remove-prop-types',
                                    'transform-react-constant-elements',
                                    'transform-decorators-legacy'
                                ]
                            }
                        }
                    }
                }
            },
            {
                test: /\.scss$/,
                use: extractCSS.extract([
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true,
                            minimize: {
                                autoprefixer: {
                                    add: true,
                                    remove: true,
                                    browsers: ['last 4 versions']
                                },
                                discardComments: {
                                    removeAll: true
                                },
                                discardUnused: false,
                                mergeIdents: false,
                                reduceIdents: false,
                                safe: true,
                                sourcemap: true
                            },
                            importLoaders: 1,
                            localIdentName: '[path]___[name]__[local]___[hash:base64:5]'
                        }
                    },
                    'resolve-url-loader?sourceMap',
                    {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: true,
                            ident: 'postcss',
                            config: {
                                path: path.resolve(__dirname, 'postcss.config.js')
                            }
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            data: '$root: "/";'
                        }
                    }
                ])
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
        ]
    },
    plugins: [
        new webpack.LoaderOptionsPlugin({
            resolveUrlLoader: {
                root: path.resolve(__dirname, '../')
            }
        }),
        new webpack.ContextReplacementPlugin(/moment[/\\]locale$/, /nl/),
        new webpack.IgnorePlugin(/regenerator|nodent|js-beautify/, /ajv/),
        new webpack.DefinePlugin({
            '__DEV__': 'false',
            '__DEBUG__': 'false',
            'NODE_ENV': JSON.stringify('production'),
            'process.env.NODE_ENV': JSON.stringify('production')
        }),
        new webpack.optimize.OccurrenceOrderPlugin(),
        extractCSS,
        new BundleAnalyzerPlugin({
            // Start analyzer HTTP-server.
            // You can use this plugin to just generate Webpack Stats JSON file by setting `startAnalyzer` to `false`
            // and `generateStatsFile` to `true`.
            startAnalyzer: false,
            // Analyzer HTTP-server port
            analyzerPort: 8888,
            // Automatically open analyzer page in default browser if `startAnalyzer` is `true`
            openAnalyzer: true,
            // If `true`, Webpack Stats JSON file will be generated in bundles output directory
            generateStatsFile: true,
            // Name of Webpack Stats JSON file that will be generated if `generateStatsFile` is `true`.
            // Relative to bundles output directory.
            statsFilename: 'bundle.stats.json'
        })
    ]
};
