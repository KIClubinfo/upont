// Création des diverses taches
module.exports = function(grunt) {
    grunt.initConfig({
        // Vérifie le JS
        jshint: {
            all: ['app/js/*.js', 'app/js/**/*.js']
        },
        // Uglify le JS
        uglify: {
            build: {
                files: {
                    'www/upont.min.js': ['app/js/app.js', 'app/js/notifications.js', 'app/js/**/*.js', 'app/js/*.js']
                }
            }
        },
        // Génère le CSS
        less: {
            build: {
                files: {
                    'www/style.css': ['app/css/*.less', 'app/css/*.css']
                }
            }
        },
        // Minifie le CSS aini généré
        cssmin: {
            build: {
                files: {
                    'www/style.min.css': ['www/style.css']
                }
            }
        },
        // Permet de surveiller les fichiers et de reexecuter les taches ci-dessus
        watch: {
            css: {
                files: ['app/css/**/*.css'],
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
