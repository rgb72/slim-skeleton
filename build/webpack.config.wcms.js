const path = require('path')
const webpack = require('webpack')
const merge = require('webpack-merge')
const ExtractTextPlugin = require('extract-text-webpack-plugin')

const { resolve } = require('./util')
const webpackConfig = require('./webpack.config.base')

require('dotenv').config({
    path: resolve('../.env')
})

module.exports = merge(webpackConfig, {
    entry: {
        app: resolve('../public/wcms/src/main.js')
    },
    output: {
        path: resolve('../public/wcms/dist'),
        publicPath: process.env.BASE_PATH + '/wcms/dist/'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    transformToRequire: {
                        img: 'src'
                    },
                    loaders: {
                        scss: ExtractTextPlugin.extract({
                            use: 'css-loader!sass-loader',
                            fallback: 'vue-style-loader'
                        }),
                        sass: ExtractTextPlugin.extract({
                            use: 'css-loader!sass-loader?indentedSyntax=true',
                            fallback: 'vue-style-loader'
                        }),
                        styl: ExtractTextPlugin.extract({
                            use: 'css-loader!stylus-loader?indentedSyntax=true',
                            fallback: 'vue-style-loader'
                        })
                    }
                }
            }
        ]
    },
    plugins: [
        new webpack.LoaderOptionsPlugin({
            options: {
                vue: {
                    loaders: {
                        js: 'babel-loader'
                    }
                }
            }
        })
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@': resolve('../public/wcms/src'),
            'vue$': 'vue/dist/vue.common.js'
        }
    }
})
