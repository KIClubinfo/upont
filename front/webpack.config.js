const path = require('path');
const webpack = require('webpack');

const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    resolve: {
        alias: {
            upont: path.resolve(__dirname, 'src/upont/'),
            libs: path.resolve(__dirname, 'src/libs/')
        },
        modules: ['node_modules']
    },
    devtool: 'source-map',
    entry: {
        upont: path.resolve(__dirname, 'src/upont/js/app.js'),
        'theme_classic': path.resolve(__dirname, 'src/upont/css/themes/classic.less'),
        'theme_classic-dark': path.resolve(__dirname, 'src/upont/css/themes/classic-dark.less'),
        'theme_grey-dark': path.resolve(__dirname, 'src/upont/css/themes/grey-dark.less'),
        'theme_grey': path.resolve(__dirname, 'src/upont/css/themes/grey.less'),
        'theme_grey-green': path.resolve(__dirname, 'src/upont/css/themes/grey-green.less'),
        'theme_grey-red': path.resolve(__dirname, 'src/upont/css/themes/grey-red.less'),
        'theme_grey-yellow': path.resolve(__dirname, 'src/upont/css/themes/grey-yellow.less'),
        'theme_green': path.resolve(__dirname, 'src/upont/css/themes/green.less'),
        'theme_brown': path.resolve(__dirname, 'src/upont/css/themes/brown.less'),
        'theme_brown-dark': path.resolve(__dirname, 'src/upont/css/themes/brown-dark.less'),
        'theme_orange': path.resolve(__dirname, 'src/upont/css/themes/orange.less'),
        'theme_violet-dark': path.resolve(__dirname, 'src/upont/css/themes/violet-dark.less'),
    },
    module: {
        rules: [
            {
                test: /src\/upont\/.*\.js$/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            // presets: ['@babel/preset-env'],
                            plugins: [require('babel-plugin-angularjs-annotate')],
                        }
                    },
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
                use: ['url-loader?limit=10000&mimetype=application/font-woff']
            }, {
                test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                use: ['file-loader']
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
                use: ['file-loader']
            }
        ]
    },
    plugins: [
        new webpack.EnvironmentPlugin({
            NODE_ENV: 'development', // use 'development' unless process.env.NODE_ENV is defined
        }),
        new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /en|fr/),
        new ExtractTextPlugin({filename: '[name].min.css', allChunks: true}),
        new webpack.ProvidePlugin({$: "jquery", jQuery: "jquery"}),
        // Automatically move all modules defined outside of application directory to vendor bundle.
        new webpack.optimize.CommonsChunkPlugin({
            minChunks: (module, count) => module.resource && module.resource.indexOf(path.resolve(__dirname, 'src')) === -1,
            name: 'vendors'
        })
    ]
};
