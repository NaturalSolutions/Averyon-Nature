module.exports = function(grunt) {
   grunt.initConfig({

     /*==========  Watch Tasks  ==========*/
     watch: {
       options: {
         nospawn: true,
         livereload: false
       },
       configFiles: {
         files: ['Gruntfile.js'],
         options: {
           reload: true
         }
       },
       css: {
         files: 'less/**/*.less',
         tasks: ['less']
       }       
     },
     less: {
       dist: {          
          files: {
            'css/style.css': 'less/style.less'
          },          
          options: {
              compress: false,              
              sourceMap: true,
              sourceMapFilename: 'css/style.css.map',
              sourceMapURL: 'style.css.map',              
              outputSourceFiles: true
          },          
         
       }
     }
   });

   /*==========  Loaded Tasks  ==========*/
   grunt.loadNpmTasks('grunt-contrib-watch');
   grunt.loadNpmTasks('grunt-contrib-less');


 };
