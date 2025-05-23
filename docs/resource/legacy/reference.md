## Configuration Reference

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

{% code %}
```yaml
sylius_resource:
    resources:
        app.book:
            driver: doctrine/orm
            classes:
                model: # Required!
                interface: ~
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                factory: Sylius\Component\Resource\Factory\Factory
                form: Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType
                    validation_groups: [sylius]
            templates:
                form: Book/_form.html.twig
            translation:
                classes:
                    model: ~
                    interface: ~
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType
                        validation_groups: [sylius]
                templates:
                    form: Book/Translation/_form.html.twig
```
{% endcode %}

## Routing Generator Configuration Reference

{% code %}
```yaml
app_book:
    resource: |
        alias: app.book
        path: library
        identifier: code
        criteria:
            code: $code
        section: admin
        templates: :Book
        form: App/Form/Type/SimpleBookType
        redirect: create
        except: ['show']
        only: ['create', 'index']
        serialization_version: 1
    type: sylius.resource
```
{% endcode %}

**[Go back to the documentation's index](index.md)**
