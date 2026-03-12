# Customizing the menu

## How to customize the sidebar menu

### Decorate the sidebar menu

<div data-full-width="false">

<figure><img src="../../.gitbook/assets/sidebar_menu.png" alt="Sidebar menu"></figure>

</div>

To customize the admin menu, you need to listen for the `sylius_admin_ui.menu.event.main` event. This way, you can implement
multiple listeners e.g. in different bounded contexts of your application.

```php
declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\Event\MenuBuilderEvent;
use Sylius\AdminUi\Knp\Menu\MenuBuilder;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(MenuBuilder::EVENT_NAME)]
final readonly class MenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->addChild('dashboard', [
                'route' => 'sylius_admin_ui_dashboard',
            ])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'tabler:dashboard')
        ;
    }
}
```

### Add submenu items

<div data-full-width="false">

<figure><img src="../../.gitbook/assets/submenu_items.png" alt="Submenu items"></figure>

</div>

Now you can add submenu items:

```php
// ...
#[AsEventListener(MenuBuilder::EVENT_NAME)]
final readonly class MenuListener
{
    // ...
    
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        // ...
        $this->addLibrarySubMenu($menu);
    }
    
    private function addLibrarySubMenu(ItemInterface $menu): void
    {
        $library = $menu
            ->addChild('library')
            ->setLabel('app.ui.library')
            ->setLabelAttribute('icon', 'tabler:books')
        ;

        $library->addChild('books', ['route' => 'app_admin_book_index'])
            ->setLabel('app.ui.books')
            ->setLabelAttribute('icon', 'book')
        ;
    }
}
```

{% hint style="success" %}
**🧠 Collapse your custom menu by default**

It's possible to expand your parent menu category on page load by default. For that, you have to set the `setExtra` attribute like this:

```php
$library = $menu
    ->addChild('library')
    ->setLabel('app.ui.library')
    ->setLabelAttribute('icon', 'tabler:books')
    ->setExtra('always_open', true);
```

However, ensure that you set the attribute in the parent menu, not in one of the child menu items.
{% endhint %}
