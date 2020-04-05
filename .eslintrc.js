module.exports = {
    root: true,
    ignorePatterns: ["gulpfile.js", "webpack.config.js", "assets/js/build/", "assets/js/src/code-modal.js", "node_modules/", "vendor/"],
    extends: [
		'plugin:@wordpress/eslint-plugin/recommended'
	]
};