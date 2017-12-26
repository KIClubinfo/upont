var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var filter = require('gulp-filter');
var fs = require('fs');
var gulp = require('gulp');
var gutil = require('gulp-util');
var htmlReplace = require('gulp-html-replace');
var jshint = require('gulp-jshint');
var less = require('gulp-less');
var path = require('path');
var sourcemaps = require('gulp-sourcemaps');
var templateCache = require('gulp-angular-templatecache');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var webpack = require('webpack-stream');


/**
 * Vérifie la syntaxe JS
 */
gulp.task('lint-js', function() {
    var appFiles = [
        'src/app/js/app.js',
        'src/app/js/*.js',
        'src/app/js/**/*.js',
        'src/app/js/controllers/**/*.js'
    ];
    return gulp.src(appFiles)
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
    ;
});

/**
 * Construit le fichier JS
 */
gulp.task('build-js', function() {
    // On exclut donc jquery du main dans le bower.json et on l'introduit manuellement
    var redactor = [
        'src/libs/redactor/redactor.js',
        'src/libs/redactor/table.js',
        'src/libs/redactor/video.js',
        'src/libs/redactor/fr.js'
    ];
    var appFiles = [
        'src/app/js/app.js',
        'src/app/js/*.js',
        'src/app/js/services/*.js',
        'src/app/js/config/*.js',
        'src/app/js/directives/**/*.js',
        'src/app/js/filters/**/*.js',
        'src/app/js/controllers/**/*.js',
    ];
    var filesArray = appFiles; // redactor.concat(appFiles);

    gulp.src('public/**/*').pipe(gulp.dest('dist/'));

    var upont = gulp.src(filesArray)
        // .pipe(concat('upont.js'))
    ;

    return gulp.src('src/app/js/app.js')
        .pipe(webpack(require('./webpack.config.js')))
        .pipe(gulp.dest('dist/'))
    ;
});

/**
 * Liste les fichiers d'un répertoire
 * @param  {string} dir Le dossier
 * @return {string[]}   La liste des fichiers
 */
function getFiles(dir) {
    return fs
        .readdirSync(dir)
        .filter(function(file) {
            return !fs.statSync(path.join(dir, file)).isDirectory();
        });
}
var themesPath = 'src/app/css/main/themes/';

/**
 * Construit le CSS des thèmes de uPont en créeant un fichier CSS par thème
 */
gulp.task('build-css-main', function() {
    var themeFiles = getFiles(themesPath);


    var tasks = themeFiles.map(function(file) {
        return gulp.src(themesPath + file)
            .pipe(filter(['**/*.css', '**/*.less']))
            .pipe(less())
            .pipe(concat(file.replace(/less/, '') + 'min.css'))
            .pipe(autoprefixer({
                cascade: false
            }))
            .pipe(process.env.NODE_ENV === 'production' ? uglifycss() : gutil.noop())
            .pipe(gulp.dest('dist/themes/'))
        ;
   });
});

/**
 * Construit le CSS de l'animation de chargement comprenant la bibliothèque loading.io : https://loading.io/animation/
 */
gulp.task('build-css-loading', function(){
    return gulp.src('src/app/css/loading/*')
        .pipe(filter(['**/*.css', '**/*.less']))
        .pipe(less())
        .pipe(concat('loading.min.css'))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(process.env.NODE_ENV === 'production' ? uglifycss() : gutil.noop())
        .pipe(gulp.dest('dist/'))
});


gulp.task('build-css', ['build-css-main', 'build-css-loading']);


/**
 * Récupère les vues, les compile et les met dans le cache Angular
 */
gulp.task('build-templates', function(){
    gulp.src([
        'src/app/js/*.html',
        'src/app/js/**/*.html',
    ])
    .pipe(templateCache({
        module: 'templates',
        standalone: true
    }))
    .pipe(gulp.dest('dist/'));
});

/**
 * Définition du WATCH
 */
gulp.task('watch', function() {
    gulp.watch(['src/app/js/**/*.js', 'src/app/js/*.js'], ['lint-js', 'build-js']);
    gulp.watch(['src/app/js/*.html', 'src/app/js/**/*.html'], ['build-templates']);
    gulp.watch(['src/app/css/main/**/*.less', 'src/app/css/loading/*'], ['build-css']);
});

/**
 * Définition du BUILD
 */
gulp.task('build', [
    'build-js',
    'build-css',
    'build-templates',
    'copy-fonts'
]);

/**
 * Tache par défaut
 */
gulp.task('default', ['build', 'watch']);
