# Migrating from BootstrapAdminUi

The theme keeps the hook names, hookable names and macro APIs of `sylius/bootstrap-admin-ui`: most of a migration
is a search and replace.

## Steps

1. **Swap the packages** (see [Installation](getting-started.md)):

   ```bash
   composer remove sylius/bootstrap-admin-ui
   composer require sylius/shadcn-admin-ui
   ```

2. **Replace the namespace** in your templates and hook configuration:

   ```diff
   - {% import '@SyliusBootstrapAdminUi/shared/helper/button.html.twig' as button %}
   + {% import '@SyliusShadcnAdminUi/shared/helper/button.html.twig' as button %}
   ```

3. **Replace the component prefix**:

   ```diff
   - <twig:sylius_bootstrap_admin_ui:delete_modal … />
   + <twig:sylius_shadcn_admin_ui:delete_modal … />
   ```

4. **Rewrite Bootstrap classes** of your custom templates with Tailwind utilities.

5. **Replace the Bootstrap JavaScript**: it is not loaded, so the `data-bs-*` attributes of your templates stop
   working, without any error.

   | BootstrapAdminUi               | Shadcn Admin UI                                                     |
   |--------------------------------|---------------------------------------------------------------------|
   | `data-bs-toggle="modal"`       | `<twig:shadcn:Dialog>` or `<twig:shadcn:AlertDialog>`               |
   | `data-bs-toggle="tab"`         | The `admin--tabs` controller (see [Tabs](components.md#tabs))       |
   | `data-bs-toggle="dropdown"`    | `<twig:shadcn:DropdownMenu>` or the `dropdown` macros               |
   | `data-bs-toggle="tooltip"`     | `<twig:shadcn:Tooltip>`, or a `title` attribute                     |
   | `data-bs-toggle="collapse"`    | `<twig:shadcn:Collapsible>` or the `accordion` macro                |
   | `data-bs-dismiss="modal"`      | `<twig:shadcn:AlertDialog:Cancel>`, or `dialog_close_attrs` of `<twig:shadcn:Dialog:Close>` on a button |
   | `data-bs-dismiss="alert"`      | `data-controller="admin--dismissible"` on the alert and `data-action="admin--dismissible#close"` on its close button |

6. **Icons**: nothing to do. `tabler:*` names keep working through UX Icons; the theme itself uses `lucide:*`.

{% hint style="info" %}
Find what is left to migrate:

```bash
grep -rn "SyliusBootstrapAdminUi\|sylius_bootstrap_admin_ui\|data-bs-" templates config
```
{% endhint %}

## Equivalences

| BootstrapAdminUi                           | Shadcn Admin UI                           |
|--------------------------------------------|-------------------------------------------|
| `@SyliusBootstrapAdminUi/…`                | `@SyliusShadcnAdminUi/…`                   |
| `sylius_bootstrap_admin_ui:*` components   | `sylius_shadcn_admin_ui:*` components            |
| Bootstrap 5 / Tabler classes               | Tailwind CSS v4 utilities                 |
| `tabler:*` icons                           | `lucide:*` (`tabler:*` still works)       |
| Webpack Encore / AssetMapper               | AssetMapper + `symfonycasts/tailwind-bundle` |

## Hook differences

| Change     | Hook / hookable                                                                     |
|------------|-------------------------------------------------------------------------------------|
| ➖ Removed | `toggle_button` hookable of the sidebar                                             |
| ➖ Removed | `symfony_ux` hookables of `sylius_admin.base#stylesheets` and `sylius_admin.base#javascripts` (Stimulus and the Symfony UX controllers are started by the `admin` importmap entrypoint, the Tom Select styles are in `theme.css`) |
| ➕ Added   | `sylius_admin.common.component.navbar.sidebar_trigger` (the toggle, now in the navbar) |
| ➕ Added   | `sylius_admin.base#metatags.color_scheme`                                           |
| ➕ Added   | `sylius_admin.base#stylesheets.themes` (the color themes, see [Theming](theming.md)) |

{% hint style="info" %}
List the hookables of a page to check your overrides:
`php bin/console sylius:debug:twig-hooks sylius_admin.common.index`
{% endhint %}
