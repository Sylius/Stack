# Configuration

## Configuration reference

Full default configuration of the bundle:

{% code title="config/packages/sylius_shadcn_admin_ui.yaml" lineNumbers="true" %}
```yaml
sylius_shadcn_admin_ui:
    sidebar:
        variant: sidebar            # sidebar | floating | inset
```
{% endcode %}

{% hint style="info" %}
`php bin/console config:dump-reference sylius_shadcn_admin_ui` prints this reference with every option documented.
{% endhint %}

## Branding: logo, name, title

{% code title="config/packages/sylius_twig_hooks.yaml" lineNumbers="true" %}
```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.common.component.sidebar.logo':
            image:
                configuration:
                    name: 'Acme'
                    subtitle: 'Back office'
                    icon: 'lucide:store'   # or image: 'images/logo.svg'
        'sylius_admin.security.login.page.logo':
            image:
                configuration: { name: 'Acme', icon: 'lucide:store' }
        'sylius_admin.base#base_title':
            default:
                configuration: { title: 'Acme' }
```
{% endcode %}

| Hook                                          | Hookable  | Options                                       |
|-----------------------------------------------|-----------|-----------------------------------------------|
| `sylius_admin.common.component.sidebar.logo`  | `image`   | `name`, `subtitle`, `icon` **or** `image`     |
| `sylius_admin.security.login.page.logo`       | `image`   | `name`, `icon` **or** `image`                 |
| `sylius_admin.base#base_title`                | `default` | `title` (browser tab)                         |

## Stylesheet and entrypoint paths

By default the layout loads `styles/admin.css` and the `admin` entrypoint. To use other names:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.base#stylesheets':
            styles: { configuration: { path: 'styles/back-office.css' } }
        'sylius_admin.base#javascripts':
            javascripts: { configuration: { entrypoint: 'back-office' } }
```

## Sidebar variant

| Variant    | Look                                                                 |
|------------|----------------------------------------------------------------------|
| `sidebar`  | Default. Attached to the edge of the screen.                         |
| `floating` | Detached card with rounded corners and a shadow.                     |
| `inset`    | The page content becomes a rounded panel next to the sidebar.        |

Set it for every admin page:

{% code title="config/packages/sylius_shadcn_admin_ui.yaml" lineNumbers="true" %}
```yaml
sylius_shadcn_admin_ui:
    sidebar:
        variant: inset
```
{% endcode %}

The value is set on the `sidebar` hookable of every page hook that renders it (`index`, `show`, `create`,
`update`, `dashboard`), so it can still be overridden page by page:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.common.show':
            sidebar:
                configuration: { variant: 'floating' }
```

{% hint style="info" %}
Keyboard shortcut: <kbd>Ctrl</kbd>/<kbd>⌘</kbd> + <kbd>B</kbd> collapses the sidebar to icons. Its state is kept in a
cookie; on mobile, the sidebar opens as a drawer.
{% endhint %}
