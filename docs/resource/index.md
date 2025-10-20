# SyliusResourceBundle

The **Sylius Resource Bundle** provides a powerful and extensible foundation for exposing your **business resources** (entities, aggregates, etc.) in a declarative way.  
Rather than generating controllers or relying on rigid admin generators, it offers a flexible architecture that lets you focus on your domain model while the bundle handles the boilerplate.

A *Resource* is any business object you want to expose — for example, a `Product`, `Order`, or `UserProfile`.  

Each resource can define a set of **operations** — actions that can be performed on it.  
Typical operations include `index`, `show`, `create`, `update`, and `delete`, but you can also define custom, domain-specific operations too.  
The bundle orchestrates each operation through a well-defined lifecycle involving **providers**, **processors**, and **responders**:

- **Providers** are responsible for loading or creating the resource object and **validating it** (ensuring the object is consistent before any business logic is applied).
    - Example: load from Doctrine, create a new instance, hydrate from request data, validate the object, or fetch from an external API.
- **Processors** handle the business logic or persistence layer (e.g. saving, executing domain services, dispatching events).
- **Responders** produce the final response (e.g. rendering a template).

This architecture allows you to use the bundle in two main ways:

- **Rapid Application Mode (RAD)** – perfect for quick CRUD setup with Doctrine ORM. You define your entity, mark it as a resource, and everything just works.
- **Domain-Driven Design / Advanced Mode** – where you control how data is provided and processed by writing your own providers and processors.

In short, the Sylius Resource Bundle is both **declarative** and **extensible** — define your resources and their operations, and let the framework handle the rest, while still giving you full control over the domain logic when you need it.

## Resource system for Symfony applications.

* [Installation](installation.md)

# New documentation
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
