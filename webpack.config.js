const path = require('path')
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')

module.exports = {
  mode: process.env.NODE_ENV,
  devtool: process.env.NODE_ENV === 'production' ? false : 'cheap-module-eval-source-map',
  entry: [
    path.resolve(__dirname, './resources/assets/js/voyager.js'),
    path.resolve(__dirname, './resources/assets/sass/voyager.scss')
  ],
  output: {
    path: path.resolve(__dirname, './resources/assets/dist'),
    filename: 'js/voyager.js'
  },
  resolve: {
    alias: {
      'components': path.resolve(__dirname, './resources/assets/components'),
      'directives': path.resolve(__dirname, './resources/assets/js/directives'),
      'mixins': path.resolve(__dirname, './resources/assets/js/mixins'),
      'vue': 'vue/dist/vue.esm-browser.js'
    },
    extensions: ['.vue', '.js', '.json', '.scss'],
  },
  performance: {
    hints: false,
  },
  optimization: {
    minimize: process.env.NODE_ENV === 'production',
    minimizer: [new TerserPlugin(), new CssMinimizerPlugin()],
  },
  stats: {
    hash: false,
    version: false,
    timings: false,
    children: false,
    errorDetails: false,
    entrypoints: false,
    performance: process.env.NODE_ENV === 'production',
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
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: { hmr: process.env.NODE_ENV === 'development' }
          },
          'css-loader'
        ]
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          {
            loader: 'css-loader',
            options: { url: false, importLoaders: 1 }
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
      filename: 'css/voyager.css'
    }),
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: true,
      __VUE_PROD_DEVTOOLS__: false
    })
  ],
};