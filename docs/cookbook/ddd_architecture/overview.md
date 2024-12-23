# Architecture overview

{% hint style="info" %}
The whole codebase of the DDD application example is available on [Github](https://github.com/loic425/sylius-stack-ddd).
{% endhint %}

Here is the folder structure of the "BookStore" domain:

```txt
src
└── BookStore
    ├── Application
    ├── Domain
    └── Infrastructure
        └── Sylius
```

* The `src/BookStore/Domain` directory contains your business code
* The `src/BookStore/Application` contains your application code that is not related to any framework/package
* the `src/BookStore/Infrastructure/Sylius` directory contains everything that is related to Sylius packages
