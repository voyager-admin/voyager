const path = require('path')
const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = (env = {}) => ({
  mode: env.prod ? 'production' : 'development',
  devtool: env.prod ? false : 'cheap-module-eval-source-map',
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
      vue: env.prod ? 'vue/dist/vue.esm-browser.prod.js' : 'vue/dist/vue.esm-browser.js'
    },
    extensions: ['.vue', '.js', '.json', '.scss'],
  },
  performance: {
    hints: false,
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
            options: { hmr: !env.prod }
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