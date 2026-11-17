# Menu

## Adding items

Unchanged from BootstrapAdminUi: decorate `sylius_admin_ui.knp.menu_builder`.

```php
use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'sylius_admin_ui.knp.menu_builder')]
final class AdminMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private readonly MenuBuilderInterface $menuBuilder,
    ) {}

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $menu->addChild('dashboard', ['route' => 'sylius_admin_ui_dashboard'])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'lucide:layout-dashboard')
        ;

        return $menu;
    }
}
```

{% hint style="info" %}
Labels are translation keys. The test application (`src/ShadcnAdminUi/tests/Functional/.application/src/Menu/AdminMenuBuilder.php`) shows every
item type below.
{% endhint %}

## Item types

```text
┌─────────────────────────────┐
│ ▣ Dashboard                 │  ← link (level 1)
│ ▤ Library                 › │  ← collapsible item
│   │ Books                   │     ← child links
│   │ New book                │
│                             │
│ SECTION                     │  ← section (extra 'section')
│ ☰ Submenu                 › │
│   ○ SECTION 1               │     ← header (extra 'header')
│   │  │ Item 1               │     ← indented under the header
│ ▢ Documentation         ↗   │  ← external link (target _blank)
└─────────────────────────────┘
```

### Link

```php
$menu->addChild('dashboard', ['route' => 'sylius_admin_ui_dashboard'])
    ->setLabel('sylius.ui.dashboard')
    ->setLabelAttribute('icon', 'lucide:layout-dashboard')
;
```

### Collapsible item

A top-level item with children becomes a collapsible item. It opens by itself when one of its children is the current
page.

```php
$library = $menu->addChild('library')
    ->setLabel('app.ui.library')
    ->setLabelAttribute('icon', 'lucide:library')
;
$library->addChild('books', ['route' => 'app_admin_book_index'])->setLabel('app.ui.books');
$library->addChild('new_book', ['route' => 'app_admin_book_create'])->setLabel('app.ui.new_book');
```

{% hint style="info" %}
Only two levels are rendered: the children of a child are ignored.
{% endhint %}

### Section

A top-level item with the `section` extra is not a link: it starts a new group of the sidebar, titled with its label.
The items before the first section go in an untitled group.

```php
$menu->addChild('section', ['extras' => ['section' => true]])
    ->setLabel('app.ui.section')
;
```

### Header inside a collapsible item

A child with the `header` extra is a sub-section title, drawn as a node on the vertical line of the sub menu. The
children that follow it are indented under it, as a tree branch.

```php
$subMenu = $menu->addChild('submenu')
    ->setLabel('app.ui.submenu')
    ->setLabelAttribute('icon', 'lucide:menu')
;
$subMenu->addChild('section_1', ['extras' => ['header' => true]])->setLabel('app.ui.section_1');
$subMenu->addChild('item_1', ['route' => 'app_admin_book_index'])->setLabel('app.ui.item_1');
```

### External link

`target="_blank"` adds `rel="noopener"` and an external link icon.

```php
$menu->addChild('docs', ['uri' => 'https://stack.sylius.com', 'linkAttributes' => ['target' => '_blank']])
    ->setLabel('sylius.ui.documentation')
    ->setLabelAttribute('icon', 'lucide:book-open')
;
```

## Options reference

| Option                                                   | Level        | Effect                                                         |
|----------------------------------------------------------|--------------|----------------------------------------------------------------|
| `setLabelAttribute('icon', 'lucide:…')`                  | 1            | Icon in front of the label                                     |
| `linkAttributes: {target: '_blank'}`                     | 1, 2         | Opens in a new tab, with an external link icon (`_self` by default) |
| `extras: {section: true}`                                | 1            | Starts a titled group instead of rendering a link              |
| `extras: {header: true}`                                 | 2            | Sub-section title inside a collapsible item                    |
| `extras: {always_open: true}`                            | 1            | Collapsible item always expanded on page load                  |
| `extras: {translation_domain: '…'}`                      | 1, 2         | Translation domain of the label (`messages` by default, `false` for no translation) |
| `extras: {translation_params: {…}}`                      | 1, 2         | Parameters of the label translation                            |
| `attributes: {class: '…'}`                               | 1, 2         | Classes added to the `<li>`                                    |

The `extras` options can also be set with `setExtra('section', true)`, `linkAttributes` with
`setLinkAttribute('target', '_blank')`.

{% hint style="info" %}
Icons are Symfony UX Icons names. Lock new ones with `php bin/console ux:icons:lock` before deploying.
{% endhint %}

## Behaviour

- **Collapsed sidebar** (<kbd>Ctrl</kbd>/<kbd>⌘</kbd> + <kbd>B</kbd>): only the icons stay visible. A level 1 item without
  icon shows the first letter of its label. Clicking a collapsible item expands the sidebar.
- **Search**: the field above the menu filters the items while typing; sections and headers with no matching item
  are hidden.
- **Current page**: the matching item is highlighted (`aria-current="page"`) and its parent opened.
