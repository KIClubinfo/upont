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

gulp.task('build-css', function() {
    return gulp.src(['app/css/*.less', 'www/libs/scheduler/codebase/dhtmlxscheduler.css'])
        .pipe(less())
        .pipe(concat('style.min.css'))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gutil.env.type === 'production' ? minifyCSS() : gutil.noop())
        .pipe(gulp.dest('www/'));
});

gulp.task('build-js', function() {
    return gulp.src(['app/js/app.js', 'app/js/**/*.js', 'app/js/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(concat('upont.min.js'))
        .pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
        .pipe(gulp.dest('www'));
});

gulp.task('unit-tests', function() {
    return gulp.src('foobar') //On met une source nulle pour que karma charge son propre fichier de config
        .pipe(karma({
            configFile: 'karma.conf.js',
            action: 'watch'
        }));
});

gulp.task('e2e-tests', function() {

});

gulp.task('watch', function() {
    gulp.watch(['app/js/**/*.js', 'app/js/*.js'], ['build-js']);
    gulp.watch('app/css/*.less', ['build-css']);
});

gulp.task('default', ['build-js', 'build-css', 'watch']);
