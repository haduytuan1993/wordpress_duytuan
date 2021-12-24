
var debug = process.env.NODE_ENV !== "production";

module.exports = {
  context: __dirname,
  // devtool: debug ? "inline-sourcemap" : null,
  // This is the "main" file which should include all other modules
  entry: {
    admin: './assets/src/js/admin.js',
    front: './assets/src/js/front.js'
  },
  // Where should the compiled file go?
  output: {
    // To the `dist` folder
    path: __dirname + '/assets/js/',
    // With the filename `build.js` so it's dist/build.js
    filename: '[name].min.js'
  },
  module: {
    // Special compilation rules
    loaders: [
      {
        // Ask webpack to check: If this file ends with .js, then apply some transforms
        test: /\.js$/,
        // Transform it with babel
        loader: 'babel-loader',
        // don't transform node_modules folder (which don't need to be compiled)
        exclude: /node_modules/
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
            loaders: {
                js: 'babel-loader'
            }
        },
        exclude: /node_modules/
      }
    ]
  },
  plugins: debug ? [] : [
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.OccurenceOrderPlugin(),
    // new webpack.optimize.UglifyJsPlugin({ mangle: false, sourcemap: false }),
    new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /de|en/)
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.js'
    }
  }

}