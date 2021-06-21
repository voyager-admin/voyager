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
            static: false,
            headers: {
                'Access-Control-Allow-Origin': '*'
            },
            firewall: false,
            port: 8080,
        },
        entry: {
            // Currently, HMR does not work (https://github.com/webpack/webpack-dev-server/issues/2692) when using multiple entries 🤷🏼
            icons: path.resolve(__dirname, './resources/assets/js/icons.js'),
            voyager: path.resolve(__dirname, './resources/assets/js/voyager.js'),
        },
        output: {
            path: path.resolve(__dirname, './resources/assets/dist'),
            filename: 'js/[name].js'
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/assets/js'),
                '@components': path.resolve(__dirname, './resources/assets/components'),
                '@directives': path.resolve(__dirname, './resources/assets/js/directives'),
                '@helper': path.resolve(__dirname, './resources/assets/js/helper'),
                '@mixins': path.resolve(__dirname, './resources/assets/js/mixins'),
                '@sassmixins': path.resolve(__dirname, './resources/assets/sass/mixins'),
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
                                implementation: require('sass'),
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
            new webpack.optimize.LimitChunkCountPlugin({
                maxChunks: 1
            }),
        ],
    };
}