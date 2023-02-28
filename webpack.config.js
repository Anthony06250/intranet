const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
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

    // Field js file
    .addEntry('field/boolean', './assets/js/field/field.boolean.js')
    .addEntry('field/customer', './assets/js/field/field.customer.js')
    .addEntry('field/integer', './assets/js/field/field.integer.js')
    .addEntry('field/money', './assets/js/field/field.money.js')
    .addEntry('field/percent', './assets/js/field/field.percent.js')
    .addEntry('field/select2', './assets/js/field/field.select2.js')

    // Component js file
    .addEntry('component/notification', './assets/js/ui/component.notification.js')
    .addEntry('component/datatables', './assets/js/ui/component.datatables.js')

    // Css file
    .addStyleEntry('css/app-pdf', './assets/scss/app-pdf.scss')
    .addStyleEntry('css/icons', './assets/scss/icons.scss')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    .enablePostCssLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // you can use this method to provide other common global variables,
    // such as '_' for the 'underscore' library
    .autoProvideVariables({
        bootstrap: 'bootstrap',
        'window.bootstrap': 'bootstrap',
    })

    // Add webpack plugin
    .addPlugin(new CopyWebpackPlugin({
        patterns: [
            {from: './assets/images', to: 'images'}
        ]
    }))
;

module.exports = Encore.getWebpackConfig();
