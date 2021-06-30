const path = require('path');
const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

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
            port: 8081,
            host: '127.0.0.1'
        },
        entry: {
            voyager: path.resolve(__dirname, './resources/assets/js/voyager.js')
        },
        output: {
            path: path.resolve(__dirname, './resources/assets/dist'),
            filename: 'js/[name].js',
            chunkFilename: (pathData) => {
                if (pathData.chunk.name) {
                    if (pathData.chunk.name.includes('Icon')) {
                        return `js/icons/${pathData.chunk.name.replace('Icon', '')}.js?ver=[chunkhash]`;
                    } else if (pathData.chunk.name.startsWith('Bread')) {
                        return 'js/bread/[name].js?ver=[chunkhash]';
                    } else if (pathData.chunk.name.startsWith('Formfield')) {
                        return 'js/formfields/[name].js?ver=[chunkhash]';
                    }
                }
                return 'js/chunks/[name].js?ver=[chunkhash]';
            },
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
            splitChunks: { chunks: 'async' }
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
                filename: 'css/voyager.css'
            }),
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: true,
                __VUE_PROD_DEVTOOLS__: false
            })
        ],
    };
}