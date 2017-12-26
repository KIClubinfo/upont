var path = require('path');
var webpack = require('webpack');

module.exports = {
    // resolve: {
    //     modules: [path.resolve('./app'), path.resolve('./node_modules')]
    // },
    devtool: 'source-map',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'upont.min.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                enforce: 'pre',
                use: [
                    {
                        loader: 'baggage-loader?[file].html&[file].less'
                    }, {
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
                test: /\.(png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$/,
                use: [{ loader: 'file-loader' }]
            }
        ]
    },
    plugins: [new webpack.ProvidePlugin({$: "jquery", jQuery: "jquery"})]
};
