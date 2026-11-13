# Copy to clipboard

Use `copy_to_clipboard` for a standalone copy button, or `copyable` to display a value or custom inline content next to the button. Both copy the explicit `value` prop, never the rendered content.

## Installation

Install Bootstrap Admin UI before using the component:

```bash
composer require sylius/bootstrap-admin-ui
```

The components use Symfony UX Twig Component, Stimulus, UX Icons and the Bootstrap Admin UI/Tabler styles. Bootstrap CSS alone does not provide all the default button styles.

With the standard Bootstrap Admin UI layout and compiled assets, the controller is registered by the package's `symfony_ux` entrypoint. If your application starts its own Stimulus application, follow [JavaScript integration](#javascript-integration) below instead.

## Usage

Pass a literal value with the `value` prop:

```twig
<span>ABC-123</span>

<twig:sylius_bootstrap_admin_ui:copy_to_clipboard
    value="ABC-123"
/>
```

For a Twig expression, prefix the prop with `:`:

```twig
<span>{{ item.code }}</span>

<twig:sylius_bootstrap_admin_ui:copy_to_clipboard
    :value="item.code"
/>
```

### Display a value with its copy button

```twig
<twig:sylius_bootstrap_admin_ui:copyable :value="item.code" />
```

For a badge, a formatted ID, a truncated label or an email link, provide custom content:

```twig
<twig:sylius_bootstrap_admin_ui:copyable :value="customer.email">
    <a href="mailto:{{ customer.email }}">{{ customer.email }}</a>
</twig:sylius_bootstrap_admin_ui:copyable>
```

The body overrides the `content` block. The copy button remains a sibling of the content, not a child of the link. Only `customer.email` is copied, independently of the displayed text. Use inline content and do not place either component inside another button or link.

The default displayed value is escaped and is not automatically translated. Translate or format the display in the content block when needed; explicitly pass a translated `value` if that is also what should be copied.

### Missing values and disabling copying

Omitting `value`, passing `null`, or passing an empty string disables the button. Custom content remains visible. Both numeric `0` and string `"0"` are copyable. Pass IDs with significant leading zeros as strings.

Both components also accept `disabled` to explicitly disable the button. They do not read from inputs or watch your application data: update the component's props when re-rendering dynamic values.

### Twig Hooks

Both components can be rendered directly as Twig Hook components. They consume the injected hook metadata without rendering it as an HTML attribute. Pass the value explicitly through the props:

```yaml
sylius_twig_hooks:
    hooks:
        'app.example':
            copy:
                component: 'sylius_bootstrap_admin_ui:copy_to_clipboard'
                props:
                    value: '@=_context.item.getCode()'
```

Use `sylius_bootstrap_admin_ui:copyable` instead to include the default text display. For custom markup, use a hook template containing the component and its body.

### Button attributes

Additional component attributes are forwarded to the button. Default button classes are preserved when adding custom classes:

```twig
<twig:sylius_bootstrap_admin_ui:copy_to_clipboard
    :value="item.code"
    class="btn-sm"
    data-test-copy-code
/>
```

For `copyable`, ordinary attributes apply to the outer container. Prefix attributes with `button:` to apply them to the copy button:

```twig
<twig:sylius_bootstrap_admin_ui:copyable
    :value="item.code"
    class="text-nowrap"
    button:class="custom-copy-button"
    button:data-test-copy-code
/>
```

Both components accept `button_class` to **replace** the default classes, while `class` on the standalone button (or `button:class` on `copyable`) adds classes. For example, use `button_class="btn btn-sm btn-outline-secondary"` for standard Bootstrap button styling without Tabler's ghost-button classes.

### Translations

The component uses these translation keys by default:

- `sylius.ui.copy_to_clipboard`
- `sylius.ui.copied_to_clipboard`
- `sylius.ui.unable_to_copy_to_clipboard`

Override those keys in your application's translation files, or pass alternative translation keys with the label props:

```twig
<twig:sylius_bootstrap_admin_ui:copy_to_clipboard
    :value="item.code"
    copy_label="app.ui.copy_code"
    copied_label="app.ui.code_copied"
    error_label="app.ui.unable_to_copy_code"
/>
```

### Feedback and dynamic content

The button uses a native `title`, not a Bootstrap tooltip, so no tooltip initialization is needed when content is inserted dynamically. The controller connects through Stimulus as usual.

During a copy, repeated clicks are ignored and the button exposes `aria-busy` without disabling it or taking away keyboard focus. Success changes the icon and announces the translated message through a live region. Failure also displays the translated error text. Feedback resets after two seconds; a new copy replaces previous feedback. Disconnecting cancels pending UI updates and timers, not the browser's clipboard operation itself.

## JavaScript integration

Register the controller **once in the application's existing Stimulus application**. Do not start a second application just for this component. Both Twig components use the identifier `copy-to-clipboard`.

### AssetMapper

Follow the [AssetMapper setup](../../getting-started.md#using-assetmapper) to disable the package's `symfony_ux` entrypoint when starting your own Stimulus application.

The package exposes its controllers to AssetMapper. Enable the controller in your `assets/controllers.json` file, StimulusBundle then registers it with the `copy-to-clipboard` identifier:

{% code title="assets/controllers.json" lineNumbers="true" %}
```json
{
    "controllers": {
        "@sylius/bootstrap-admin-ui": {
            "copy-to-clipboard": {
                "enabled": true,
                "fetch": "eager"
            }
        }
    }
}
```
{% endcode %}

See the [Assets](../assets.md#stimulus-controllers) page for the other controllers of the package. The application's importmap must also provide `@hotwired/stimulus`, as in a normal Symfony Stimulus installation.

### Webpack Encore / an existing Sylius Stimulus application

Import the controller source and register it on your existing application. From a project-level `assets/bootstrap.js`, the import is:

```js
import CopyToClipboardController from '../vendor/sylius/bootstrap-admin-ui/assets/controllers/copy-to-clipboard-controller.js';

app.register('copy-to-clipboard', CopyToClipboardController);
```

Here `app` is your existing Stimulus application. Adjust the relative import path to your entrypoint location and rebuild the application's assets. Do not import the package's entire `symfony_ux` entrypoint into an application that already starts Stimulus.

### Sylius compatibility

Match the Bootstrap Admin UI package version to the target Sylius version before installing it. This branch requires `sylius/twig-hooks ^0.12`, whereas the Sylius 2.1 and 2.2 AdminBundle branches require `^0.8` and `^0.9` respectively. These constraints do not overlap; controller registration alone does not solve that Composer incompatibility.

Reusing this functionality in those versions requires a compatible backport or dependency alignment. It does not require replacing the application's admin layout. When integrating the templates selectively, also configure the anonymous component prefix, UX Icons and the translations rather than importing the whole Bootstrap Admin UI layout configuration.

## Browser requirements

Copying uses the browser's Clipboard API. This API requires a secure context, which normally means HTTPS in production or localhost during development.

Clipboard permissions and embedding policies can still prevent copying. The components report failure rather than falling back to deprecated clipboard APIs. Values passed to the component are present in the page's HTML; do not pass secrets or data the current user is not authorized to see.

## Tests

From `src/BootstrapAdminUi`, run `vendor/bin/phpunit` for the rendering and Twig Hooks tests, and `yarn test` for the controller unit tests using Node.js 24 and the existing JavaScript dependencies. The latter use DOM doubles; actual browser permissions and assistive-technology behavior still require browser-level testing.
