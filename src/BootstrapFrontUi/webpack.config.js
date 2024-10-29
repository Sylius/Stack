var Encore = require('@symfony/webpack-encore');
const path = require("path");

Encore
  .setOutputPath('./public/')
  .setPublicPath('./')
  .setManifestKeyPrefix('bundles/SyliusBootstrapFrontUi')

  .cleanupOutputBeforeBuild()
  .enableSassLoader()
  .enableSourceMaps(false)
  .enableVersioning(false)
  .disableSingleRuntimeChunk()

  .copyFiles({
       from: './assets/images',
       to: 'images/[path][name].[ext]',
  })

  .addEntry('app', './assets/entrypoint.js')
  .enableSassLoader()
  .autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
