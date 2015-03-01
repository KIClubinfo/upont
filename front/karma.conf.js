// Karma configuration
// Generated on Sun Feb 22 2015 17:59:14 GMT+0100 (CET)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
    // //All algular files
    'www/libs/jquery/dist/jquery.min.js',
    'www/libs/angular/angular.min.js',
    'www/libs/angular-i18n/angular-locale_fr-fr.js',
    'www/libs/angular-animate/angular-animate.js',
    'www/libs/angular-sanitize/angular-sanitize.js',
    'www/libs/angular-strap/dist/angular-strap.min.js',
    'www/libs/angular-strap/dist/angular-strap.tpl.min.js',
    'www/libs/angular-ui-router/release/angular-ui-router.min.js',
    'www/libs/angular-resource/angular-resource.min.js',
    'www/libs/angular-loading-bar/build/loading-bar.min.js',
    'www/libs/angular-jwt/dist/angular-jwt.js',
    'www/libs/scheduler/codebase/dhtmlxscheduler.js',
    'www/libs/scheduler/codebase/locale/locale_fr.js',
    'www/libs/js-base64/base64.min.js',
    'www/libs/angular-mocks/angular-mocks.js',

    //Template files
    'www/views/**/*.html',
    'www/views/*.html',

    //Upont files
    // 'www/upont.min.js',
    'app/js/*.js',
    'app/js/**/*.js',

    // //Test files
    'app/tests/unit/*.js'
    ],


    // list of files to exclude
    exclude: [
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
        'app/js/app.js': 'coverage',
        'app/js/services.js': 'coverage',
        'app/js/directives/*.js': 'coverage',
        'app/js/controllers/*.js': 'coverage',
        "www/views/**/*.html": ["ng-html2js"],
        "www/views/*.html": ["ng-html2js"]
    },

    ngHtml2JsPreprocessor: {
        stripPrefix: "www/",
        moduleName: 'templates'
    },

    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['dots', 'coverage'],

    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: false,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false
  });
};
