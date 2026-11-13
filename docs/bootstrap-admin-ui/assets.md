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

They are registered by the prebuilt `symfony_ux` Stimulus application. When you disable it, register them in your own application by adding the `@sylius/bootstrap-admin-ui` package to your `assets/controllers.json` file:

{% code title="assets/controllers.json" lineNumbers="true" %}
```json
{
    "controllers": {
        "@sylius/bootstrap-admin-ui": {
            "copy-to-clipboard": {
                "enabled": true,
                "fetch": "eager"
            },
            "theme-switcher": {
                "enabled": true,
                "fetch": "eager"
            }
        }
    }
}
```
{% endcode %}

{% hint style="warning" %}
If they are not registered, the matching components are still rendered but do nothing (for instance, the dark/light theme button of the navbar), and no error is reported.
{% endhint %}

### Versions without the `@sylius/bootstrap-admin-ui` controllers package

Older versions of the package do not expose their controllers to AssetMapper, so the `controllers.json` entry above fails. Register the controllers directory yourself instead: StimulusBundle then registers them with their file names (`copy-to-clipboard`, `theme-switcher`).

{% code title="config/packages/asset_mapper.yaml" lineNumbers="true" %}
```yaml
framework:
    asset_mapper:
        paths:
            assets/: ''
            vendor/sylius/bootstrap-admin-ui/assets/controllers: sylius-bootstrap-admin-ui/controllers
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
