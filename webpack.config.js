var Encore = require('@symfony/webpack-encore');
//var webpack = require('webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');
Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)

    .addEntry('impulse', [
        './js/includes.js',
        './js/globals.js'
    ])

    .addEntry('home', [
        './js/home.js'
    ])

    .addStyleEntry('style', [
        './scss/style.scss'
    ])

    // uncomment if you use Sass/SCSS fileste
    .enableSassLoader(function(options) {
        // options.includePaths = [...]
    }, {
        //for prod
        resolveUrlLoader: !Encore.isProduction()
        //for dev
    })
    .enableVersioning()
    .enableLessLoader()
    .splitEntryChunks()
;

let config = Encore.getWebpackConfig();

config.name = 'zemoga';

module.exports = [config];
