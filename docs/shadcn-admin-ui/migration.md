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

5. **Icons**: nothing to do. `tabler:*` names keep working through UX Icons; the theme itself uses `lucide:*`.

{% hint style="info" %}
Find what is left to migrate:

```bash
grep -rn "SyliusBootstrapAdminUi\|sylius_bootstrap_admin_ui" templates config
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
| ➕ Added   | `sylius_admin.common.component.navbar.sidebar_trigger` (the toggle, now in the navbar) |
| ➕ Added   | `sylius_admin.base#metatags.color_scheme`                                           |

{% hint style="info" %}
List the hookables of a page to check your overrides:
`php bin/console sylius:debug:twig-hooks sylius_admin.common.index`
{% endhint %}
