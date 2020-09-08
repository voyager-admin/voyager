const path = require('path')
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = (env, argv) => ({
  mode: argv.mode,
  devtool: argv.mode === 'production' ? false : 'cheap-module-eval-source-map',
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
    },
    extensions: ['.vue', '.js', '.json', '.scss'],
  },
  performance: {
    hints: false,
  },
  stats: {
    hash: false,
    version: false,
    timings: false,
    children: false,
    errorDetails: false,
    entrypoints: false,
    performance: argv.mode === 'production',
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
            options: { hmr: argv.mode !== 'production' }
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
      __VUE_OPTIONS_API__: 'true',
      __VUE_PROD_DEVTOOLS__: 'false'
    })
  ],
});