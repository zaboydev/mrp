var gulp = require("gulp"),
    sass = require("gulp-sass"),
    minify = require("gulp-minify-css"),
    rename = require('gulp-rename'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer');

gulp.task("compile", function() {
  gulp.src('checkbox3.sass')
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([ autoprefixer({ browsers: ['> 1%', 'last 2 versions', 'Firefox < 20','ie 6-8'] }) ]))
    .pipe(gulp.dest('dist'))
    .pipe(minify({compatibility: 'ie8'}))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('dist'));

  return gulp.src(['./dist/checkbox3.min.css',]).pipe(gulp.dest('./public/css/'))
});

gulp.task("lib", function() {
  gulp.src(['./dist/checkbox3.min.css',]).pipe(gulp.dest('./public/css/'))
  gulp.src(['./bower_components/fontawesome/css/font-awesome.min.css',]).pipe(gulp.dest('./public/css/'))
  gulp.src(['./bower_components/fontawesome/fonts/*']).pipe(gulp.dest('./public/fonts'))
  return gulp.src(['./bower_components/bootstrap/dist/css/bootstrap.min.css',]).pipe(gulp.dest('./public/css/'))
});

gulp.task("watch", function() {
  gulp.watch("checkbox3.sass", ['compile'])
});
