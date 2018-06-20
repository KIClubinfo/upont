'use strict';

const path = require('path');
const url = require('url');
const webpack = require('webpack');

const webpackConfig = require('./webpack.config');

const HotModuleReplacementPlugin = webpack.HotModuleReplacementPlugin; // Hot reloading and inline style replacement

webpackConfig.devServer = {
    compress: true,
    contentBase: path.join(__dirname, 'public'),
    historyApiFallback: true,
    hot: true,
    inline: true,
    noInfo: true,
    port: 8080,
    public: 'localhost:8080',
    watchContentBase: true
};

webpackConfig.devtool = 'inline-source-map';

webpackConfig.output = {
    filename: '[name].min.js',
    path: path.resolve(__dirname, 'dev'),
    publicPath: '/'
};

webpackConfig.plugins.push(new HotModuleReplacementPlugin());

module.exports = webpackConfig;
