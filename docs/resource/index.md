# SyliusResourceBundle

There are plenty of things you need to handle for every single Resource in your web application.

Several "Admin Generators" are available for Symfony, but we needed something really simple, that will allow us to have reusable controllers
but preserve the performance and standard Symfony workflow. We did not want to generate any code or write "Admin" class definitions in PHP.
The big goal was to have exactly the same workflow as with writing controllers manually but without actually creating them!

Another idea was not to limit ourselves to a single persistence backend.
``Resource`` component provides us with generic purpose persistence services and you can use this bundle with multiple persistence backends.
So far we support:

* Doctrine ORM
* Doctrine MongoDB ODM
* Doctrine PHPCR ODM

## Resource system for Symfony applications.

* [Installation](installation.md)

# New documentation
* [Resource lifecycle](lifecycle.md)
* [Create a new resource](create_new_resource.md)
* [Configure your resource](configure_your_resource.md)
* [Configure your operations](configure_your_operations.md)
* [Validation](validation.md)
* [Redirect](redirect.md)
* [Resource Factories](resource_factories.md)
* [Providers](providers.md)
* [Processors](processors.md)
* [Responders](responders.md)

# Deprecated documentation
* [Legacy Resource Documentation](legacy/index.md)

## Learn more

* [Resource Layer in the Sylius platform](https://docs.sylius.com/the-book/architecture/resource-layer) - concept documentation
