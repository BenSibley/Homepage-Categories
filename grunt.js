module.exports = function(grunt) {

    // 1. All configuration goes here 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            dist: {
                files: {
                    'js/functions.min.js' : 'js/functions.js'
                }
            }
        },
        cssmin: {
            combine: {
                files: {
                    'css/style.min.css': ['css/style.css'],
                }
            }
        },
        watch: {
            scripts: {
                files: ['js/*.js'],
                tasks: ['uglify'],
                options: {
                    spawn: false
                }
            },
            css: {
                files: ['sass/*.scss'],
                tasks: ['cssmin'],
                options: {
                    livereload: true,
                    spawn: false
                }
            }
        },
        makepot: {
            target: {
                options: {
                    domainPath: '/languages',
                    potFilename: 'homepage-categories.pot',
                    type: 'wp-plugin'
                }
            }
        }
    });

    // 3. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-wp-i18n');

    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['uglify', 'watch', 'cssmin', 'makepot']);

};