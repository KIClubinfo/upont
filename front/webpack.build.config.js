'use strict';

const path    = require('path');
const webpack = require('webpack');

const config = require('./webpack.config');

const UglifyJsPlugin = webpack.optimize.UglifyJsPlugin;
const uglifyOptions  = {
  mangle : {
    except : ['$super', '$', 'exports', 'require', 'angular']
  }
};

config.output = {
  filename   : '[name].min.js',
  path       : path.resolve(__dirname, 'dist'),
  publicPath : ''
};

// config.plugins.push(new UglifyJsPlugin(uglifyOptions));

module.exports = config;
