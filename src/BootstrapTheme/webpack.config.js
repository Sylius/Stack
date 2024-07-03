var Encore = require('@symfony/webpack-encore');
const WebpackRTLPlugin = require('webpack-rtl-plugin');

Encore
  .setOutputPath('./public/')
  .setPublicPath('./')
  .setManifestKeyPrefix('bundles/SyliusBootstrapTheme')

  .cleanupOutputBeforeBuild()
  .enableSassLoader()
  .enableSourceMaps(false)
  .enableVersioning(false)
  .disableSingleRuntimeChunk()

  // copy FontAwesome fonts
  .copyFiles({
    from: './node_modules/@fortawesome/fontawesome-free/webfonts/',
    // relative to the output dir
    to: 'fonts/[name].[hash].[ext]'
  })

  // copy flag images for country type
  // .copyFiles({
  //     from: './assets/images/flags/',
  //     to: 'images/flags/[path][name].[ext]',
  //     pattern: /\.png$/
  // })

  // copy images
  .copyFiles({
       from: './assets/images',
       to: 'images/[path][name].[ext]',
  })

  .addPlugin(new WebpackRTLPlugin())

  .addEntry('app', './assets/scripts/app.js')
  .enableSassLoader((options) => {
    //options.additionalData = '@import "~semantic-ui-css/semantic.min.css";';
  })
  .autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
