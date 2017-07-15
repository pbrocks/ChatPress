'use strict';
module.exports = function(grunt) {

  grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

	// js minification
    uglify: {
      dist: {
        files: {
          'library/js/chatpress.min.js': [
            '.dev/js/chatpress.js',
          ],
        }
      }
    },

    // Autoprefixer for our CSS files
    postcss: {
      options: {
        map: true,
        processors: [
          require('autoprefixer-core') ({
            browsers: ['last 2 versions']
          })
        ]
      },
      dist: {
        src: ['library/css/*.css']
      }
    },
    auto_install: {
      local: {}
    },

    // css minify all contents of our directory and add .min.css extension
    cssmin: {
      target: {
        files: [
          {
						'library/css/style.min.css':
						[
							'library/css/style.css',
						],
          },
        ]
      }
    },

		replace: {
			base_file: {
				src: [ 'php-notifier.php' ],
				overwrite: true,
				replacements: [
					{
						from: /Version:     (.*)/,
						to: "Version:     <%= pkg.version %>"
					},
					{
						from: /define\(\s*'PHP_NOTIFIER_VERSION',\s*'(.*)'\s*\);/,
						to: "define( 'PHP_NOTIFIER_VERSION', '<%= pkg.version %>' );"
					}
				]
			},
			// replace tested up to in readme
			readme_txt: {
				src: [ 'readme.txt' ],
				overwrite: true,
				replacements: [
					{
						from: /Tested up to:      (.*)/,
						to: "Tested up to:      <%= pkg.tested_up_to %>"
					},
					{
						from: /Stable tag:        (.*)/,
						to: "Stable tag:        <%= pkg.version %>"
					}
				]
			},

		// Generate a nice banner for our css/js files
		usebanner: {
	    taskName: {
	      options: {
	        position: 'top',
					replace: true,
	        banner: '/*\n'+
						' * @Plugin <%= pkg.title %>\n' +
						' * @Author <%= pkg.author %>\n'+
						' * @Site <%= pkg.site %>\n'+
						' * @Version <%= pkg.version %>\n' +
		        ' * @Build <%= grunt.template.today("mm-dd-yyyy") %>\n'+
						' */',
	        linebreak: true
	      },
	      files: {
	        src: [
						'library/css/*.min.css',
						'library/js/*.min.js',
					]
	      }
	    }
	  },

    // watch our project for changes
    watch: {
      css: {
        files: [
					'.dev/sass/admin/*.scss',
				],
        tasks: [ 'sass', 'cssmin', 'usebanner' ],
        options: {
          spawn: false,
          event: [ 'all' ]
        },
      },
			js: {
        files: [
					'.dev/js/*.js',
					'! .dev/js/*.min.js',
				],
        tasks: [ 'uglify', 'usebanner', 'copy:js' ],
        options: {
          spawn: false,
          event: [ 'all' ]
        },
      },
    },

		copy: {
			main: {
				files: [
					{
						expand: true,
						src: [
							'library/*',
							'! library/.DS_Store',
							'partials/',
							'php-notifier.php',
							'readme.txt'
						],
						dest: 'build/php-notifier/',
					},
				],
			},
			js: {
				files: [
					{
						expand: true,
						flatten: true,
						filter: 'isFile',
						src: [
							'.dev/js/php-notifier-notices.js',
						],
						dest: 'library/js',
					},
				],
			},
		},

		clean: {
			build: [ 'build/*' ],
			zip:   [
				'build/php-notifier/',
				'build/.DS_Store'
			],
		},

		compress: {
		  main: {
		    options: {
		      archive: 'build/php-notifier-v<%= pkg.version %>.zip'
		    },
		    files: [
		      {
						cwd: 'build/php-notifier/',
						dest: 'php-notifier/',
						src: [ '**' ]
					}
		    ]
		  }
		},

		shell: {
			docs: [
				'git clone -b gh-pages https://github.com/CodeParrots/php-notifier.git documentation',
			].join( '&&' )
		}

	});

	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-banner' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-text-replace' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-menu' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-shell' );

	grunt.registerTask( 'default', [ 'menu' ] );

	// Run Grunt.js
	grunt.registerTask( 'Run Grunt.js Tasks', 'Default grunt task.', [
		'sass',
		'postcss',
		'cssmin',
		'uglify',
		'copy:js',
		'usebanner',
		'watch',
	] );

	// Bump Version & Tested up to version
	grunt.registerTask( 'Bump Version', 'Bump the version and tested up to version.', [
		'replace',
	] );

	// Generate documentation into the /documentation/ directory
	grunt.registerTask( 'Generate Documentation', 'Generate documentation into the /documentation/ directory.', [
		'shell:docs',
	] );

	// Build Task
	grunt.registerTask( 'Build Package', 'Build the package into the /build/ directory.', [
		'sass',
		'postcss',
		'cssmin',
		'uglify',
		'copy:js',
		'usebanner',
		'replace',
		'clean',
		'copy:main',
		'compress',
	] );

};
