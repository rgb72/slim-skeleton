require('dotenv').config({
    path: '../.env'
})

const webpack = require('webpack')
const merge = require('webpack-merge')

const OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')

const { parseEntry, resolve } = require('./util')
const entry = parseEntry(require('./entry/site.json'), resolve('../public/assets/src'))

require('dotenv').config()

const webpackEnvConfig = process.env.ENVIRONMENT === 'development' ? require('./webpack.config.development') : require('./webpack.config.production')

module.exports = merge({
    entry,
    output: {
        path: resolve('../public/assets/dist'),
        publicPath: process.env.BASE_PATH + '/assets/dist/',
        filename: '[name]/main.js',
        chunkFilename: '[name].js?[hash:7]'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.s[a|c]ss$/,
                loader: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader!sass-loader?indentedSyntax=true'
                })
            },
            {
                test: /\.css$/,
                loader: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader'
                })
            },
            {
                test: /\.(woff2?|eot|ttf|otf|svg)(\?.*)?$/,
                loader: 'url-loader',
                options: {
                    limit: 10000,
                    name: 'fonts/[name].[ext]?[hash:7]'
                }
            },
            {
                test: /\.(png|jpe?g|gif)(\?.*)?$/,
                loader: 'url-loader',
                options: {
                    limit: 10000,
                    name: 'images/[name].[ext]?[hash:7]'
                }
            }
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery",
            Popper: ['popper.js', 'default']
        }),
        new OptimizeCSSPlugin({
            cssProcessorOptions: {
                safe: true
            }
        }),
        new ExtractTextPlugin({
            filename: '[name]/main.css'
        })
    ],
    resolve: {
        alias: {
            '@': resolve('../public/assets/src'),
            'jquery-ui/ui/widget': 'blueimp-file-upload/js/vendor/jquery.ui.widget.js'
        },
        extensions: ['.js', '.json']
    }
}, webpackEnvConfig)
