// Defining requirements
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const sass = require('gulp-sass')(require('node-sass'));
const babel = require('gulp-babel');
const postcss = require('gulp-postcss');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const watch = require('gulp-watch');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const ignore = require('gulp-ignore');
const rimraf = require('gulp-rimraf');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();
const del = require('del');
const gulpSequence = require('gulp-sequence');
const replace = require('gulp-replace');
const autoprefixer = require('autoprefixer');
const fs = require('fs');
const path = require('path');

// Configuration file to keep your code DRY
const cfg = require('./gulpconfig.json');
const paths = cfg.paths;

// Run:
// gulp sass
// Compiles SCSS files in CSS and merge into folder_name.css
gulp.task('sass', function (done) {
	return gulp
		.src(paths.scss + '/**/*.scss')
		.pipe(
			plumber({
				errorHandler(err) {
					console.log(err);
					this.emit('end');
				},
			})
		)
		.pipe(sourcemaps.init())
		.pipe(sass({ errLogToConsole: true }))
		.pipe(postcss([autoprefixer()]))
		.pipe(concat('style.css'))
		.pipe(sourcemaps.write(undefined, { sourceRoot: './' }))
		.pipe(gulp.dest(paths.css))
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(cleanCSS({ compatibility: '*' }))
		.pipe(
			plumber({
				errorHandler(err) {
					console.log(err);
					this.emit('end');
				},
			})
		)
		.pipe(rename({ suffix: '.min' }))
		.pipe(sourcemaps.write(''))
		.pipe(gulp.dest(paths.css));
});

// Run:
// gulp js
// Uglifies and concat all JS files and merge into folder_name.js
gulp.task('scripts', function (done) {
	return gulp
		.src(paths.script + '/**/*.js')
		.pipe(
			babel({
				presets: ['@babel/preset-env'],
			})
		)
		.pipe(concat('script.js'))
		.pipe(gulp.dest(paths.js))
		.pipe(uglify())
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest(paths.js));
});

// Run:
// gulp compile
gulp.task('compile', function (callback) {
	gulp.series('sass', 'scripts')(callback);
});

// Run:
// gulp watch
// Starts watcher. Watcher runs gulp sass task on changes
gulp.task('watch', function () {
	gulp.watch(`${paths.scss}/**/*.scss`, gulp.series('sass'));
	gulp.watch(`${paths.script}/**/*.js`, gulp.series('scripts'));
});

// Run:
// gulp browser-sync
// Starts browser-sync task for starting the server.
gulp.task('browser-sync', function () {
	browserSync.init(cfg.browserSyncWatchFiles, cfg.browserSyncOptions);
});

// Run:
// gulp watch-bs
// Starts watcher with browser-sync. Browser-sync reloads page automatically on your browser
gulp.task('watch-bs', gulp.parallel('browser-sync', 'watch'));

// Run:
// gulp
// Starts watcher (default task)
gulp.task('default', gulp.series('watch'));
