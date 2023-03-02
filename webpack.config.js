const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    /*
     * ENTRY CONFIG
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('hyper-config', './assets/js/hyper-config.js')
    // Page js files
    .addEntry('page/advances-payments', './assets/js/page/page.advances-payments.js')
    .addEntry('page/buybacks', './assets/js/page/page.buybacks.js')
    .addEntry('page/controls', './assets/js/page/page.controls.js')
    .addEntry('page/deposits-sales', './assets/js/page/page.deposits-sales.js')
    .addEntry('page/invoices', './assets/js/page/page.invoices.js')
    .addEntry('page/safes', './assets/js/page/page.safes.js')
    .addEntry('page/safes-controls', './assets/js/page/page.safes-controls.js')
    // Field js files
    .addEntry('field/boolean', './assets/js/field/field.boolean.js')
    .addEntry('field/customer', './assets/js/field/field.customer.js')
    .addEntry('field/integer', './assets/js/field/field.integer.js')
    .addEntry('field/money', './assets/js/field/field.money.js')
    .addEntry('field/percent', './assets/js/field/field.percent.js')
    // Plugin js files
    .addEntry('plugin/notification', './assets/js/plugin/plugin.notification.js')
    .addEntry('plugin/datatables', './assets/js/plugin/plugin.datatables.js')
    .addEntry('plugin/select2', './assets/js/plugin/plugin.select2.js')
    // Css files
    .addStyleEntry('css/app', './assets/scss/app.scss')
    .addStyleEntry('css/app-pdf', './assets/scss/app-pdf.scss')
    .addStyleEntry('css/icons', './assets/scss/icons.scss')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    .enableSassLoader()
    .enablePostCssLoader()
    .enableIntegrityHashes(Encore.isProduction())

    .autoProvidejQuery()
    .autoProvideVariables({
        bootstrap: 'bootstrap'
    })

    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]'
    })

    .configureImageRule({
        type: 'asset',
        maxSize: 4 * 1024
    })
    .configureFontRule({
        type: 'asset',
        maxSize: 4 * 1024
    })
;

module.exports = Encore.getWebpackConfig();
