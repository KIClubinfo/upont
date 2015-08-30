var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var filter = require('gulp-filter');
var htmlReplace = require('gulp-html-replace');
var jshint = require('gulp-jshint');
var less = require('gulp-less');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var gutil = require('gulp-util');
var mainBowerFiles = require('main-bower-files');
var templateCache = require('gulp-angular-templatecache');

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
        .pipe(gutil.env.type === 'production' ? uglifycss() : gutil.noop())
        .pipe(gulp.dest('www/'));
});

gulp.task('build-js', function() {
    // On doit charger le redactor avant angular-redactor qui est dans les bowerfiles, mais redactor dépend de jquery
    // On exclut donc jquery du main dans le bower.json et on l'introduit manuellement
    var redactor = [
        'www/libs/jquery/dist/jquery.js',
        'www/libs/redactor/redactor.js',
        'www/libs/redactor/table.js',
        'www/libs/redactor/video.js',
        'www/libs/redactor/fr.js'
    ];
    var vendorsFiles = mainBowerFiles();
    var appFiles = [
        'app/js/app.js',
        'app/js/*.js',
        'app/js/**/*.js',
        'app/js/controllers/**/*.js'
    ];
    var files = redactor.concat(vendorsFiles.concat(appFiles));
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

gulp.task('templates', function(){
    gulp.src(['www/views/**/*.html'])
        .pipe(templateCache({
            module: 'templates',
            standalone: true
        }))
        .pipe(gulp.dest('./www'));
});

gulp.task('copy-fonts', function () {
    return gulp.src(mainBowerFiles())
        .pipe(filter(['**/*.eot', '**/*.svg', '**/*.ttf', '**/*.woff', '**/*.woff2', '**/*.otf']))
        .pipe(gulp.dest('www/fonts/'));
});

gulp.task('watch', function() {
    gulp.watch(['app/js/**/*.js', 'app/js/*.js'], ['lint-js', 'build-js']);
    gulp.watch('app/css/*.less', ['build-css']);
    gulp.watch(['app/index.html', 'www/views/**/*.html'], ['build-html', 'templates']);
});
gulp.task('build', ['build-js', 'build-css', 'build-html', 'templates', 'copy-fonts']);
gulp.task('default', ['build', 'watch']);
