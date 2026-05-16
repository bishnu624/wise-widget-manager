// webpack.config.js
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {
  const isProd = argv.mode === 'production';

  return {
    entry: {
      admin: path.resolve(__dirname, 'src/main.jsx'),
    },

    output: {
      path: path.resolve(__dirname, '../dist/assets'),
      filename: '[name].js',
      clean: true,
    },

    // Externalize WP packages — fixes the wp.hooks/heartbeat conflict
    externals: {
      '@wordpress/hooks': 'wp.hooks',
      '@wordpress/element': 'wp.element',
      '@wordpress/api-fetch': 'wp.apiFetch',
      react: 'React',
      'react-dom': 'ReactDOM',
    },

    resolve: {
      extensions: ['.js', '.jsx'],
    },

    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                '@babel/preset-env',
                ['@babel/preset-react', { runtime: 'automatic' }],
              ],
            },
          },
        },
        {
          test: /\.css$/,
          use: [MiniCssExtractPlugin.loader, 'css-loader'],
        },
      ],
    },

    plugins: [
      new MiniCssExtractPlugin({
        filename: 'index.css',
      }),
    ],

    devtool: isProd ? false : 'source-map',
  };
};
