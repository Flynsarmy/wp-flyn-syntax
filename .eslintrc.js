module.exports = {
    root: true,
    ignorePatterns: ["gulpfile.js", "webpack.config.js", "assets/js/build/", "node_modules/", "vendor/"],
    extends: [
      'plugin:@wordpress/eslint-plugin/recommended'
    ]
};