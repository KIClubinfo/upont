var gulp = require('gulp');
var gutil = require('gulp-util');
var concat = require('gulp-concat');
var less = require('gulp-less');
var minifyCSS = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var htmlReplace = require('gulp-html-replace');
var mainBowerFiles = require('main-bower-files');
var filter = require('gulp-filter');

gulp.task('jshint', function() {
    return gulp.src(['app/js/**/*.js', 'app/js/*.js'])
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'));
});

gulp.task('build-css', function() {
    var vendorsFiles = mainBowerFiles();
    var appFiles = [
        'app/css/upont.less'
    ];
    var files = vendorsFiles.concat(appFiles);
    return gulp.src(files)
        .pipe(filter(['**/*.css', '**/*.less']))
        .pipe(less())
        .pipe(concat('style.min.css'))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gutil.env.type === 'production' ? minifyCSS() : gutil.noop())
        .pipe(gulp.dest('www/'));
});

gulp.task('build-js', function() {
    var vendorsFiles = mainBowerFiles();
    var appFiles = [
        'www/libs/redactor/redactor.js',
        'www/libs/redactor/table.js',
        'www/libs/redactor/video.js',
        'www/libs/redactor/fr.js',
        'app/js/app.js',
        'app/js/*.js',
        'app/js/**/*.js',
        'app/js/controllers/**/*.js'
    ];
    var files = vendorsFiles.concat(appFiles);
    return gulp.src(files)
        .pipe(filter(['**/*.js', '**/*.coffee']))
        .pipe(concat('upont.min.js'))
        .pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
        .pipe(gulp.dest('www/'))
    ;
});

gulp.task('lint-js', function() {
    var appFiles = [
        'app/js/app.js',
        'app/js/*.js',
        'app/js/**/*.js',
        'app/js/controllers/**/*.js'
    ];
    return gulp.src(appFiles)
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
    ;
});

gulp.task('build-html', function(){
    return gulp.src('app/index.html')
        .pipe(gutil.env.type == "production" ? htmlReplace({base: '<base href="/">'}) : gutil.noop())
        .pipe(gulp.dest('www/'));
});

gulp.task('copy-fonts', function () {
    return gulp.src(mainBowerFiles())
        .pipe(filter(['**/*.eot', '**/*.svg', '**/*.ttf', '**/*.woff', '**/*.woff2', '**/*.otf']))
        .pipe(gulp.dest('www/fonts/'));
});

gulp.task('watch', function() {
    gulp.watch(['app/js/**/*.js', 'app/js/*.js'], ['lint-js', 'build-js']);
    gulp.watch('app/css/*.less', ['build-css']);
    gulp.watch('app/index.html', ['build-html']);
});
gulp.task('default', ['lint-js', 'build-js', 'build-css', 'build-html', 'copy-fonts', 'watch']);
