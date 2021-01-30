const path = require('path')
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')
const CopyPlugin = require('copy-webpack-plugin')

module.exports = (env, options) => {
    process.env.NODE_ENV = options.mode;

    return {
        mode: options.mode,
        devtool: options.mode === 'production' ? false : 'eval-cheap-module-source-map',
        devServer: {
            contentBase: path.resolve(__dirname, '/'),
            inline: true,
            hot: true,
            headers: {
                'Access-Control-Allow-Origin': '*'
            },
            disableHostCheck: true
        },
        entry: {
            voyager: path.resolve(__dirname, './resources/assets/js/voyager.js'),
            font: path.resolve(__dirname, './resources/assets/sass/font.scss')
        },
        output: {
            path: path.resolve(__dirname, './resources/assets/dist'),
            filename: 'js/[name].js'
        },
        resolve: {
            alias: {
                'components': path.resolve(__dirname, './resources/assets/components'),
                'directives': path.resolve(__dirname, './resources/assets/js/directives'),
                'mixins': path.resolve(__dirname, './resources/assets/js/mixins')
            },
            extensions: ['.vue', '.js', '.json', '.scss', '.tsx', '.ts'],
        },
        performance: {
            hints: false,
        },
        optimization: {
            minimize: env.production,
            minimizer: [new TerserPlugin(), new CssMinimizerPlugin()],
            removeEmptyChunks: true,
        },
        stats: {
            hash: false,
            version: false,
            timings: false,
            children: false,
            errorDetails: false,
            entrypoints: false,
            performance: env.production,
            chunks: false,
            modules: false,
            reasons: false,
            source: false,
            publicPath: false,
            builtAt: false
        },
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    loader: 'ts-loader',
                    options: {
                        appendTsSuffixTo: [/\.vue$/],
                    },
                    exclude: /node_modules/,
                },
                {
                    test: /\.vue$/,
                    use: 'vue-loader'
                },
                {
                    test: /\.css$/,
                    use: [MiniCssExtractPlugin.loader, 'css-loader']
                },
                {
                    test: /\.scss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader,
                        },
                        {
                            loader: 'css-loader',
                            options: { url: false }
                        },
                        {
                            loader: 'postcss-loader',
                            options: {}
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                implementation: require('dart-sass'),
                            }
                        }
                    ]
                },
                {
                    test: /\.svg$/,
                    use: [{ loader: 'html-loader' }]
                }
            ]
        },
        plugins: [
            new VueLoaderPlugin(),
            new MiniCssExtractPlugin({
                filename: 'css/[name].css'
            }),
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: true,
                __VUE_PROD_DEVTOOLS__: false
            }),
            new CopyPlugin({
                patterns: [
                    { from: './node_modules/inter-ui/Inter (web)', to: './fonts/inter-ui' },
                ]
            })
        ],
    };
}