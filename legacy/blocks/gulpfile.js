const gulp = require('gulp'),
  gutil = require('gulp-util'),
  sass = require('gulp-sass'),
  notify = require('gulp-notify'),
  prefixer      = require('autoprefixer'),
  postcss = require('gulp-postcss'),
  sourcemaps = require('gulp-sourcemaps');

const postconfig = [
  prefixer({
    grid: true
  })
]
gulp.task('sass', function () {
    return gulp.src('./**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      noCache: true,
      outputStyle: "expanded",
      lineNumbers: true,
      loadPath: './**/*',
      sourceMap: true
    })).on('error', function (error) {
      gutil.log(error);
      this.emit('end');
    })
    // .pipe(prefixer())
    .pipe(postcss(postconfig))
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest('.'))
    .pipe(notify({
      title: "ACF SASS Compiled",
      message: "All ACF SASS files have been recompiled to CSS.",
      onLast: true
    }));
  });
  gulp.task('watch', function() {
    gulp.watch('**/*.scss', gulp.series('sass'));
  });
  gulp.task('default', gulp.series('sass', 'watch'));