var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')

    .addEntry('app', './assets/js/app.js')
    .addEntry('daterangepicker', './assets/js/date-range-picker.js')
    .addEntry('orders', './assets/js/orders.js')
    .addEntry('order-print', './assets/js/print-order.js')
    .addEntry('userinfo', './assets/js/userinfo.js')
    .addEntry('vlans', './assets/js/vlans.js')
    .addEntry('statistics', './assets/js/statistics.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .addLoader({ test: /\.script\.js$/, loader: 'script-loader' })
    //.autoProvidejQuery()
;
module.exports = Encore.getWebpackConfig();