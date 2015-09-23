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
        sass: {
            dist: {
                files: {
                    'css/style.css': 'sass/style.scss'
                }
            }
        },
        autoprefixer: {
            dist: {
                options: {
                    browsers: ['last 1 version', '> 1%', 'ie 8']
                },
                files: {
                    'css/style.css': 'css/style.css'
                }
            }
        },
        cssjanus: {
            dev: {
                options: {
                    swapLtrRtlInUrl: false // replace 'ltr' with 'rtl'
                },
                src: ['css/style.css'],
                dest: 'css/rtl.css'
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
                tasks: ['sass', 'autoprefixer', 'cssjanus', 'cssmin'],
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
        },
        excludeFiles: '--exclude "*.gitignore" --exclude ".sass-cache/" --exclude "*.DS_Store" --exclude ".git/" --exclude ".idea/" --exclude "gruntfile.js" --exclude "node_modules/" --exclude "package.json" --exclude "sass/"',
        shell: {
            zip: {
                command: [
                    // delete existing copies (if they exist)
                    'rm -R /Users/bensibley/Documents/compete-themes/dist/homepage-categories || true',
                    'rm -R /Users/bensibley/Documents/compete-themes/dist/homepage-categories.zip || true',
                    // copy plugin folder without any project/meta files
                    'rsync -r /Applications/MAMP/htdocs/wordpress/wp-content/plugins/homepage-categories /Users/bensibley/Documents/compete-themes/dist/ <%= excludeFiles %>',
                    // open dist folder
                    'cd /Users/bensibley/Documents/compete-themes/dist/',
                    // zip the homepage-categories folder
                    'zip -r homepage-categories.zip homepage-categories'
                ].join('&&')
            }
        }
    });

    // 3. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-cssjanus');
    grunt.loadNpmTasks('grunt-shell');

    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['uglify', 'watch', 'cssmin', 'makepot', 'sass', 'autoprefixer', 'cssjanus', 'shell']);

};