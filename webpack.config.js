const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableStimulusBridge('./assets/controllers')

    // Babel configuration
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // Enable PostCSS loader to use Tailwind CSS
    .enablePostCssLoader(options => {
        options.postcssOptions = {
            config: path.resolve(__dirname, 'postcss.config.js')
        };
    })
;

module.exports = Encore.getWebpackConfig();
