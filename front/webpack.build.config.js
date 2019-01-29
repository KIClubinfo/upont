'use strict';

const path = require('path');
const webpack = require('webpack');

const CopyWebpackPlugin = require('copy-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

const config = require('./webpack.config');

config.output = {
    filename: '[name].min.js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: ''
};

config.plugins.push(
    new webpack.NoEmitOnErrorsPlugin(),
    new UglifyJsPlugin({
        sourceMap: true,
        uglifyOptions: {
            mangle: {
                reserved: ['$', 'jQuery']
            }
        }
    }),
    new CopyWebpackPlugin([
        {
            // Copy directory contents to {output}/
            from: 'public/'
        }
    ])
);

module.exports = config;
