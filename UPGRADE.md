# UPGRADE

## Unreleased

### BootstrapAdminUi

* The create/update content composition gained a `body` hookable that renders
  the Tabler `.page-body` wrapper. The `form_error_alert` and `form` hookables
  moved inside it:

  | Before | After |
  |---|---|
  | `sylius_admin.common.create.content.form_error_alert` | `sylius_admin.common.create.content.body.form_error_alert` |
  | `sylius_admin.common.create.content.form` | `sylius_admin.common.create.content.body.form` |
  | `sylius_admin.common.update.content.form_error_alert` | `sylius_admin.common.update.content.body.form_error_alert` |
  | `sylius_admin.common.update.content.form` | `sylius_admin.common.update.content.body.form` |

  Hook configuration and templates targeting these hookables or their children
  (e.g. `…content.form.sections.general`) must be retargeted accordingly. Apps
  that overrode `shared/crud/common/content/form.html.twig` to add the missing
  `.page-body` themselves should drop that override to avoid double wrapping.
