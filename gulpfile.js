const { src, dest, parallel } = require('gulp');
const uglify = require('gulp-uglify');
const { pipeline } = require('readable-stream');

function js() {
    return pipeline(
        src('node_modules/codemirror/mode/*/*.js'),
        uglify(),
        dest('assets/vendor/codemirror/mode')
    );
}

exports.js = js;