const path = require('path');
const webpack = require('webpack');

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
                    // {
                    //     loader: 'ng-annotate-loader'
                    // },
                    {
                        loader: 'babel-loader'
                    }
                ],
                exclude: /node_modules/
            }, {
                test: /\.html$/,
                use: [
                    {
                        loader: 'ngtemplate-loader?relativeTo=' + __dirname + '/'
                    }, {
                        loader: 'html-loader'
                    }
                ]
            }, {
                test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: [
                    {
                        loader: "url-loader?limit=10000&mimetype=application/font-woff"
                    }
                ]
            }, {
                test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: [
                    {
                        loader: "file-loader"
                    }
                ]
            }, {
                test: /\.css$/,
                use: [
                    {
                        loader: 'style-loader'
                    }, {
                        loader: 'css-loader'
                    }
                ]
            }, {
                test: /\.less$/,
                use: [
                    {
                        loader: 'style-loader'
                    }, {
                        loader: 'css-loader'
                    }, {
                        loader: 'less-loader'
                    }
                ]
            }, {
                test: /\.(png|jpg|jpeg|gif)$/,
                use: [
                    {
                        loader: 'file-loader'
                    }
                ]
            }
        ]
    },
    plugins: [
        new CopyWebpackPlugin([
            { // Copy directory contents to {output}/
                from: 'public/'
            }
        ]),
        new webpack.ProvidePlugin({$: "jquery", jQuery: "jquery"}), // Automatically move all modules defined outside of application directory to vendor bundle.
        new webpack.optimize.CommonsChunkPlugin({
            minChunks: (module, count) => module.resource && module.resource.indexOf(path.resolve(__dirname, 'src')) === -1,
            name: 'vendors'
        })
    ]
};
