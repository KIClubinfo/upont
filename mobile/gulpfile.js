var gulp = require('gulp');
var gutil = require('gulp-util');
var concat = require('gulp-concat');

var less = require('gulp-less');
var minifyCSS = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');

var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');

var karma = require('gulp-karma');

gulp.task('jshint', function() {
    return gulp.src(['app/js/**/*.js', 'app/js/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'));
});

gulp.task('build-css-dark', function() {
    return gulp.src(['app/css/dark.less'])
        .pipe(less())
        .pipe(concat('dark-theme.min.css'))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gutil.env.type === 'production' ? minifyCSS() : gutil.noop())
        .pipe(gulp.dest('www/styles/'));
});

gulp.task('build-css-light', function() {
    return gulp.src(['app/css/light.less'])
        .pipe(less())
        .pipe(concat('light-theme.min.css'))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gutil.env.type === 'production' ? minifyCSS() : gutil.noop())
        .pipe(gulp.dest('www/styles/'));
});

gulp.task('build-js', function() {
    return gulp.src(['app/js/app.js', 'app/js/scripts/notifications.js', 'app/js/**/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(concat('upont.min.js'))
        .pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
        .pipe(gulp.dest('www'));
});

gulp.task('watch', function() {
    gulp.watch(['app/js/**/*.js', 'app/js/*.js'], ['build-js']);
    gulp.watch('app/css/*.less', ['build-css-dark', 'build-css-light']);
});

gulp.task('default', ['build-js', 'build-css-dark', 'build-css-light', 'watch']);
