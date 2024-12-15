const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js') // Point d'entrée principal
    .enablePostCssLoader() // Activer PostCSS pour Tailwind
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSingleRuntimeChunk();

module.exports = Encore.getWebpackConfig();
