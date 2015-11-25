var gulp = require('gulp');
var config = require('../config.js');
var utils = require('../utils.js');

gulp.task('copy-lightbox-images', function(){
    return gulp.src([config.paths.src + 'lightbox/images/**/*'])
        .pipe(gulp.dest( config.paths.build + 'lightbox/images'));
});

gulp.task('build-lightbox-mobile-css', function(){
    return utils.buildCssGroup([
        config.paths.src + 'lightbox/styles/main-mobile.scss'
    ], 'lightbox-mobile', 'lightbox/css/');
});

gulp.task('build-lightbox-ie6-css', function(){
    return utils.buildCssGroup([
        config.paths.src + 'lightbox/styles/main-ie6.scss'
    ], 'lightbox-ie6', 'lightbox/css/')
});

gulp.task('build-lightbox-css', ['build-lightbox-mobile-css', 'build-lightbox-ie6-css'], function(){
    return utils.buildCssGroup([
        config.paths.src + 'lightbox/styles/main.scss'
    ], 'lightbox', 'lightbox/css/')
});

gulp.task('build-lightbox', ['copy-lightbox-images', 'build-lightbox-css'], function(){
    var lightboxGroup = [
        config.paths.dist + 'skins/lightbox/jquery.lightbox.js'
    ];

    var lightboxIE6Group = [
        config.paths.dist + 'skins/lightbox/jquery.lightbox.ie6.js'
    ];
    utils.buildJsGroup(lightboxIE6Group, 'lightboxIe6', 'lightbox/js');
    return utils.buildJsGroup(lightboxGroup, 'lightbox', 'lightbox/js');
});