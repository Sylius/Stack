# Components

## shadcn components

The shadcn kit recipes of Symfony UX Toolkit are bundled under the `shadcn:` prefix and usable in any template:

```twig
<twig:shadcn:Card>
    <twig:shadcn:Card:Header>
        <twig:shadcn:Card:Title>Stock</twig:shadcn:Card:Title>
    </twig:shadcn:Card:Header>
    <twig:shadcn:Card:Content>
        <twig:shadcn:Badge variant="secondary">42 parcels</twig:shadcn:Badge>
    </twig:shadcn:Card:Content>
</twig:shadcn:Card>
```

| Category      | Components                                                                                   |
|---------------|----------------------------------------------------------------------------------------------|
| Layout        | `Card`, `Separator`, `Sidebar`, `Item`, `Empty`, `Skeleton`                                  |
| Forms         | `Field`, `Input`, `InputGroup`, `Label`, `NativeSelect`, `Checkbox`, `RadioGroup`, `Switch`, `Textarea` |
| Actions       | `Button`, `DropdownMenu`, `Kbd`, `KbdGroup`                                                  |
| Overlays      | `Dialog`, `AlertDialog`, `Sheet`, `Tooltip`                                                  |
| Feedback      | `Alert`, `Badge`, `Spinner`                                                                  |
| Navigation    | `Breadcrumb`, `Pagination`, `Collapsible`                                                    |
| Data          | `Avatar`, `Table`                                                                            |

{% hint style="info" %}
The [shadcn kit documentation](https://ux.symfony.com/toolkit/kits/shadcn) describes the props and sub-components
of each recipe.
{% endhint %}

## Sylius components

Same components as BootstrapAdminUi, under the `sylius_shadcn_admin_ui:` prefix.

| Component                            | Purpose                                         | Main props                                                        |
|--------------------------------------|-------------------------------------------------|-------------------------------------------------------------------|
| `sylius_shadcn_admin_ui:delete_modal`       | Delete button + confirmation dialog + CSRF form | `id`, `path`, `label`, `icon`, `icon_only`, `disabled`, `form_attr` |
| `sylius_shadcn_admin_ui:confirmation_modal` | Generic confirmation dialog                     | `modal_id`, `title`, `description`, `size`; blocks `trigger`, `footer` |
| `sylius_shadcn_admin_ui:copyable`           | Value followed by a copy button                 | `value`, `copy_label`, `copied_label`, `error_label`              |
| `sylius_shadcn_admin_ui:copy_to_clipboard`  | Copy button alone                               | `value`, `button_class`, `disabled`                               |
| `sylius_shadcn_admin_ui:badge`              | Colored badge                                   | `color`, `light`, `href`, `label`                                 |
| `sylius_shadcn_admin_ui:dot`                | Status dot                                      | `color`, `pulse`                                                  |

```twig
<twig:sylius_shadcn_admin_ui:badge color="success">Paid</twig:sylius_shadcn_admin_ui:badge>
<twig:sylius_shadcn_admin_ui:badge color="orange" :light="false" label="Late" />
<twig:sylius_shadcn_admin_ui:dot color="green" pulse />

<twig:sylius_shadcn_admin_ui:delete_modal id="{{ book.id }}" path="{{ path('app_admin_book_delete', {id: book.id}) }}" />
```

`color` accepts every name of the [tone palette](theming.md#the-tone-macro). The `pulse` halo is disabled when the
user prefers reduced motion.

## Macros

Same macro APIs as `@SyliusBootstrapAdminUi`: only the namespace changes.

| Template (`@SyliusShadcnAdminUi/shared/helper/…`) | Macros                                                              |
|--------------------------------------------------|---------------------------------------------------------------------|
| `button.html.twig`                               | `default`, `primary`, `delete`, `create`, `update`, `cancel`        |
| `breadcrumbs.html.twig`                          | `breadcrumbs(items)`                                                |
| `header.html.twig`                               | `default(level, header, icon, subheader)`, `h1(header, icon, subheader)` |
| `dropdown.html.twig`                             | `default(params)`, `list(params, items)`, `item(attributes, content, variant)` |
| `table.html.twig`                                | `headers`, `row`                                                    |
| `pagination.html.twig`                           | `simple(paginator, options)`, `results_count(paginator)`            |
| `accordion.html.twig`                            | `accordion(items, open, auto_close, flush)`: `auto_close: false` keeps several items open, no `flag` item option |
| `avatar.html.twig`                               | `default(img, class, initials, size)`, `small(img, class, initials)` |
| `palette.html.twig`                              | [`tone(color, kind)`](theming.md#the-tone-macro)                    |

The button macros accept an extra `variant` param: `default`, `secondary`, `outline`, `ghost`, `destructive`, `link`.

```twig
{% import '@SyliusShadcnAdminUi/shared/helper/button.html.twig' as button %}

{{ button.primary({text: 'Export', icon: 'lucide:download', url: path('app_export')}) }}
{{ button.default({text: 'Archive', variant: 'ghost', size: 'small'}) }}
```

## Tabs

Accessible tabs with the `admin--tabs` controller (`tab` and `panel` targets):

- keyboard navigation: arrows, <kbd>Home</kbd>, <kbd>End</kbd> (roving tabindex);
- the selected tab is kept in the URL hash;
- tabs holding a validation error are flagged and opened on submit.

```twig
<div data-controller="admin--tabs">
    <div role="tablist">
        <button type="button" role="tab" aria-controls="general" data-admin--tabs-target="tab"
                data-action="admin--tabs#select keydown->admin--tabs#keydown">General</button>
        <button type="button" role="tab" aria-controls="seo" data-admin--tabs-target="tab"
                data-action="admin--tabs#select keydown->admin--tabs#keydown">SEO</button>
    </div>
    <div id="general" role="tabpanel" data-admin--tabs-target="panel">…</div>
    <div id="seo" role="tabpanel" data-admin--tabs-target="panel">…</div>
</div>
```

## Form theme

The admin forms use the shadcn form theme. Use it anywhere:

```twig
{% form_theme form '@SyliusShadcnAdminUi/shared/form_theme.html.twig' %}
```

Render a checkbox as a switch with `label_attr: {class: 'checkbox-switch'}` or `attr: {role: 'switch'}`:

```php
$builder->add('enabled', CheckboxType::class, ['attr' => ['role' => 'switch']]);
```

## Stimulus controllers

Registered automatically by the bundle.

| Identifier                                  | Role                                                             |
|---------------------------------------------|------------------------------------------------------------------|
| `admin--tabs`                               | Accessible tabs ([see above](#tabs))                             |
| `admin--accordion`                          | Keeps a single item of the `accordion()` macro open              |
| `admin--grid-selection`                     | Row selection, "select all", bulk action forms                   |
| `admin--copy-to-clipboard`                  | Copy buttons                                                     |
| `admin--dismissible`                        | Closable flash messages and alerts                               |
| `admin--menu-search`                        | Sidebar menu search                                              |
| `admin--sidebar-trigger`                    | Navbar button that toggles the sidebar                           |
| `admin--theme-switcher`                     | Light / dark switch                                              |
| `shadcn--sidebar`, `shadcn--dialog`, `shadcn--alert-dialog`, `shadcn--dropdown-menu`, `shadcn--dropdown-menu-sub`, `shadcn--collapsible`, `shadcn--tooltip`, `shadcn--input-group`, `shadcn--input-group-display` | Behaviour of the shadcn recipes |

## Symfony UX 2

The shadcn kit relies on Symfony UX 3.5+ features. Under Symfony UX 2, the package registers a compatibility extension that provides `provide()`, `inject()` and the conflict resolution of `tailwind_classes`.

{% hint style="warning" %}
Under Symfony UX 2, `tailwind_classes` assumes the classes of the caller apply to the element using the filter. Use it on the root element of your components, through `attributes.defaults()`: on an inner element, default classes conflicting with the caller classes are dropped.
{% endhint %}

## Updating the shadcn recipes

The components of `templates/components/shadcn/` and the controllers of `assets/controllers/shadcn/` are recipes of
the [shadcn kit of Symfony UX Toolkit](https://ux.symfony.com/toolkit/kits/shadcn), renamed for the bundle.
`app:shadcn:update`, a command of the test application, installs them again from `symfony/ux-toolkit` and applies the renaming.

{% hint style="warning" %}
`symfony/ux-toolkit` is a development dependency of the package, enabled in the test application. Its 3.x versions
require PHP 8.4: on an older PHP version, Composer installs v2.36.1, whose kit is outdated. Run this procedure on
PHP 8.4+.
{% endhint %}

| Recipe                                   | Bundle                                            |
|------------------------------------------|---------------------------------------------------|
| `templates/components/Button.html.twig`  | `templates/components/shadcn/Button.html.twig`    |
| `assets/controllers/dialog_controller.js`| `assets/controllers/shadcn/dialog_controller.js`  |
| `<twig:Button>`, `component('Button')`   | `<twig:shadcn:Button>`, `component('shadcn:Button')` |
| `data-controller="dialog"`, `click->dialog#open`, `data-dialog-target`, `data-dialog-open-value` | `shadcn--dialog` everywhere |

### 1. Update the kit

```bash
composer update symfony/ux-toolkit
```

### 2. Install the recipes

```bash
APP_ENV=dev php tests/Functional/.application/bin/console app:shadcn:update               # every bundled recipe
APP_ENV=dev php tests/Functional/.application/bin/console app:shadcn:update button dialog # some of them
```

{% hint style="info" %}
Without argument, the bundled recipes are deduced from the components of `templates/components/shadcn/`
(`AlertDialog.html.twig` → `alert-dialog`). Recipe dependencies (e.g. `button` for `dialog`) are installed too.
To add a new recipe, pass its name.
{% endhint %}

### 3. Review the diff

The files are **overwritten**: `git diff` shows both the changes of the kit and the local changes of the bundle that
were lost. Keep the former, restore the latter (`git checkout -p`).

Local changes to restore after an update (as of UX Toolkit 3.7):

| File                                                       | Local change                                                                 |
|------------------------------------------------------------|------------------------------------------------------------------------------|
| `DropdownMenu/{Item,CheckboxItem,RadioItem,SubTrigger}`    | `cursor-pointer` instead of `cursor-default`                                 |
| `dropdown_menu_controller.js`                              | A pointer click focuses the menu, not its first item (no highlighted item)   |

Update this table when you add or drop a local change.

{% hint style="warning" %}
Classes copied outside the components must be synced **by hand**:

- `shared/helper/ui.html.twig`: `Button`
- `shared/form_theme.html.twig`: `Field`, `Input`, `Checkbox`, `Switch`, `NativeSelect`, `Alert`
- `shared/crud/common/sidebar/menu/menu.html.twig`: `Sidebar`, `Collapsible`
- `shared/pagerfanta.html.twig`: `Pagination`, `Button`
- `shared/components/badge.html.twig`: `Badge`
- `shared/components/confirmation_modal.html.twig`: `AlertDialog`, `Dialog/Footer`
- `shared/crud/index/content/grid/filters.html.twig`: `Card`, `Collapsible`
- `shared/crud/index/content/grid/no_results.html.twig`: `Empty`
- `shared/crud/index/content/grid/no_results/image.html.twig`: `Empty/Media`
- `shared/helper/_checkbox.html.twig`: `Checkbox`
- `shared/helper/header.html.twig`: `Empty/Media`
- `shared/helper/sorting.html.twig`: `Table/Head`
- `shared/helper/table.html.twig`: `Table`
{% endhint %}

### 4. Check

Browse the test application (sidebar, menus, dialogs, forms), then run the tests:

```bash
vendor/bin/phpunit
```
