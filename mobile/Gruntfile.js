// Création des diverses taches
module.exports = function(grunt) {
    grunt.initConfig({
        // Vérifie le JS
        jshint: {
            all: ['app/js/*.js', 'app/js/**/*.js']
        },
        // Uglify le JS, L'ORDRE DES FICHIERS EST IMPORTANT !!!
        uglify: {
            build: {
                files: {
                    'www/upont.min.js': ['app/js/app.js',
                                         'app/js/scripts/notifications.js',
                                         'app/js/**/*.js']
                }
            }
        },
        // Génère le CSS
        less: {
            light: {
                files: {'www/styles/light-theme.css': ['app/css/*.less', 'app/css/*.css']},
            },
            dark: {
                files: {'www/styles/dark-theme.css': ['app/css/*.less', 'app/css/*.css']},
                options: {
                    modifyVars: { dark: true }
                }
            }
        },
        // Minifie le CSS aini généré
        cssmin: {
            build: {
                files: {
                    'www/styles/light-theme.min.css': ['www/styles/light-theme.css'],
                    'www/styles/dark-theme.min.css': ['www/styles/dark-theme.css'],
                }
            }
        },
        // Permet de surveiller les fichiers et de reexecuter les taches ci-dessus
        watch: {
            css: {
                files: ['app/css/**/*.less'],
                tasks: ['less', 'cssmin']
            },
            js: {
                files: ['app/js/**/*.js'],
                tasks: ['jshint', 'uglify']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-serve');

    grunt.registerTask('default', ['less', 'cssmin', 'jshint', 'uglify', 'watch']);
    grunt.registerTask('build', ['less', 'cssmin', 'jshint', 'uglify']);
};
