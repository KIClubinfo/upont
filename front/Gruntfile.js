// Gruntfile.js
module.exports = function(grunt) {

  grunt.initConfig({

    // JS TASKS ================================================================
    // check all js files for errors
    jshint: {
      all: ['app/js/*.js', 'app/js/**/*.js']
    },

  // take all the js files and minify them into app.min.js
    uglify: {
      build: {
        files: {
          'www/upont.min.js': ['app/js/app.js', 'app/js/**/*.js', 'app/js/*.js']
          //C'est important de charger app.js en 1e pour l'injection des dépendances
        }
      }
    },

    // CSS TASKS ===============================================================
    // process the less file to style.css

    less: {
      build: {
        files: {
          'www/style.css': 'app/css/*.less'
        }
      }
    },

  // take the processed style.css file and minify
    cssmin: {
      build: {
        files: {
          'www/style.min.css': ['www/style.css', 'www/libs/scheduler/codebase/dhtmlxscheduler.css']
        }
      }
    },

    // COOL TASKS ==============================================================
    // watch less and js files and process the above tasks
    watch: {
      less: {
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

  grunt.registerTask('default', ['less', 'cssmin', 'jshint', 'uglify', 'watch']);
  grunt.registerTask('build', ['less', 'cssmin', 'jshint', 'uglify']);

};
