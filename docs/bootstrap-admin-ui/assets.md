---
description: >-
    How the Bootstrap Admin UI package loads its stylesheets, scripts and Stimulus controllers.
---

# Assets

## Prebuilt assets (default)

The package ships prebuilt assets in its `public/` directory, installed by `assets:install` under `public/bundles/syliusbootstrapadminui/`.
They are loaded through the following hookables of the admin base layout:

| Hook                            | Hookable     | Content                                                                               |
|---------------------------------|--------------|---------------------------------------------------------------------------------------|
| `sylius_admin.base#stylesheets` | `styles`     | Tabler/Bootstrap styles of the admin panel                                            |
| `sylius_admin.base#stylesheets` | `symfony_ux` | Stylesheets of the Symfony UX packages (Tom Select, Live Components)                  |
| `sylius_admin.base#javascripts` | `javascripts`| Scripts of the admin panel                                                            |
| `sylius_admin.base#javascripts` | `symfony_ux` | A Stimulus application with the Symfony UX controllers and the package's controllers |

Nothing else is needed: this works whether your application uses Webpack Encore, AssetMapper or no frontend tooling at all.

## Using your own Stimulus application (AssetMapper)

If your application starts its own Stimulus application, for instance with AssetMapper, disable both `symfony_ux` hookables and load your importmap instead.
Follow the [Using AssetMapper](../getting-started.md#using-assetmapper) guide.

## Stimulus controllers

The package provides the following Stimulus controllers:

| Identifier          | Used by                                                                      |
|---------------------|------------------------------------------------------------------------------|
| `copy-to-clipboard` | the [copy to clipboard](components/copy-to-clipboard.md) components          |
| `theme-switcher`    | the theme switcher of the navbar                                             |

They are registered by the prebuilt `symfony_ux` Stimulus application.
When you disable it and start your own Stimulus application with AssetMapper, the package registers them in it: it exposes its `assets/controllers/` directory to AssetMapper and adds it to the `stimulus.controller_paths` option, next to your `assets/controllers/` directory. Nothing else is needed.

{% hint style="warning" %}
Do not load the prebuilt `symfony_ux` Stimulus application and your own one on the same page: both would connect these controllers (and the Symfony UX ones) to the same elements, so the theme switcher for instance would toggle twice on each click.
{% endhint %}

### Versions that do not register their controllers

Older versions of the package do not register their controllers in your Stimulus application: the matching components are rendered but do nothing (for instance, the dark/light theme button of the navbar), without any error.
Do what newer versions do in your configuration: StimulusBundle then registers them with their file names (`copy-to-clipboard`, `theme-switcher`).

{% code title="config/packages/asset_mapper.yaml" lineNumbers="true" %}
```yaml
framework:
    asset_mapper:
        paths:
            assets/: ''
            vendor/sylius/bootstrap-admin-ui/assets/controllers: '@sylius/bootstrap-admin-ui/controllers'
```
{% endcode %}

{% code title="config/packages/stimulus.yaml" lineNumbers="true" %}
```yaml
stimulus:
    controller_paths:
        - '%kernel.project_dir%/assets/controllers'
        - '%kernel.project_dir%/vendor/sylius/bootstrap-admin-ui/assets/controllers'
```
{% endcode %}

Remove this configuration once you upgrade to a version that registers its controllers.
