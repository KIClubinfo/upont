const path = require('path');
const webpack = require('webpack');

const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
    resolve: {
        alias: {
            upont: path.resolve(__dirname, 'src/app/'),
            libs: path.resolve(__dirname, 'src/libs/')
        },
        modules: ['node_modules']
    },
    devtool: 'source-map',
    entry: {
        upont: [
            'babel-polyfill',
            path.resolve(__dirname, 'src/app/js/app.js')
        ]
    },
    module: {
        rules: [
            {
                test: /src\/app\/.*\.js$/,
                use: [
                    'babel-loader',
                    // 'eslint-loader',
                ],
                exclude: /node_modules/
            }, {
                test: /\.html$/,
                use: [
                    'ngtemplate-loader?relativeTo=' + __dirname + '/',
                    'html-loader?sourceMap'
                ]
            }, {
                test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: [
                    'url-loader?limit=10000&mimetype=application/font-woff'
                ]
            }, {
                test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: [
                    'file-loader'
                ]
            }, {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader?sourceMap', 'postcss-loader?sourceMap']
                })
            }, {
                test: /\.less$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader?sourceMap', 'postcss-loader?sourceMap', 'less-loader?sourceMap']
                })
            }, {
                test: /\.(png|jpg|jpeg|gif)$/,
                use: [
                    'file-loader'
                ]
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin({filename: '[name].min.css', allChunks: true}),
        new CopyWebpackPlugin([
            {
                // Copy directory contents to {output}/
                from: 'public/'
            }
        ]),
        new webpack.ProvidePlugin({$: "jquery", jQuery: "jquery"}),
        // Automatically move all modules defined outside of application directory to vendor bundle.
        new webpack.optimize.CommonsChunkPlugin({
            minChunks: (module, count) => module.resource && module.resource.indexOf(path.resolve(__dirname, 'src')) === -1,
            name: 'vendors'
        })
    ]
};
