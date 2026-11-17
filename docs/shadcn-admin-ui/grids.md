# Grids

## What you get

Sylius grids are rendered as a shadcn data table with:

- sorting, filters panel and pagination;
- row selection with a "select all" checkbox and bulk delete;
- actions `create`, `show`, `update`, `delete` and `apply_transition` (with `symfony/workflow`);
- filters `string`, `boolean`, `date`, `enum`, `entity`, `exists`, `select`;
- a card layout below the `md` breakpoint.

The `class` option of an `apply_transition` action colors its (single-color) icon with a
[palette](theming.md#the-tone-macro) color: `success`, `danger`, `warning`, `info`, `green`, `red`… The `outline-` and
`ghost-` prefixes of BootstrapAdminUi are ignored, and an unknown color renders as `secondary`.

```php
ApplyTransitionAction::create(name: 'accept', route: 'app_admin_application_accept', options: ['class' => 'success']);
```

## Column options

Cells are `whitespace-nowrap`: the table scrolls horizontally when needed. A field's `vars` option accepts:

| Var        | Type                                     | Effect                                                                                  |
|------------|------------------------------------------|-----------------------------------------------------------------------------------------|
| `th_class` | `string`                                 | Classes added to the column header                                                      |
| `td_class` | `string`                                 | Classes added to every cell of the column                                               |
| `wrap`     | `bool`                                   | Lets a long-text column wrap (`min-w-48 whitespace-normal break-words`) instead of widening the table |
| `mobile`   | `title` \| `badge` \| `meta` \| `hidden` | Place of the field in the [mobile card](#mobile-cards)                                  |

```php
StringField::create('comment')->setOptions(['vars' => ['wrap' => true]]);
StringField::create('price')->setOptions(['vars' => ['td_class' => 'text-right tabular-nums']]);
```

## Mobile cards

Below the `md` breakpoint each row becomes a card (CSS only, same markup), where each cell is a
"label … value" line. The `mobile` var moves a field:

| Value    | Place in the card                              |
|----------|------------------------------------------------|
| `title`  | Card heading                                   |
| `badge`  | Short line under the title, e.g. a status      |
| `meta`   | Muted line, without label                      |
| `hidden` | Not shown on mobile                            |

```php
StringField::create('number')->setOptions(['vars' => ['mobile' => 'title']]);
TwigField::create('state', 'admin/order/state.html.twig')->setOptions(['vars' => ['mobile' => 'badge']]);
```

## Filters panel

The filters of a grid sit in a collapsible card, open on load when criteria are set, with a dot in its trigger. The
`filters` hookable accepts a configuration:

| Key         | Default                     | Effect                                              |
|-------------|-----------------------------|-----------------------------------------------------|
| `open`      | open when criteria are set  | `true` / `false`: state of the panel on load        |
| `indicator` | `true`                      | `false` hides the dot shown when criteria are set   |

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.common.index.content.grid':
            filters:
                configuration:
                    open: false
```

Two hooks let you add content to the panel, with the context of the index page (`resources`):

| Hook                                                        | Place                                                      |
|-------------------------------------------------------------|------------------------------------------------------------|
| `sylius_admin.common.index.content.grid.filters#trigger`    | Inside the trigger, after its title (e.g. a counter)       |
| `sylius_admin.common.index.content.grid.filters#summary`    | Between the trigger and the form, shown even when collapsed |
