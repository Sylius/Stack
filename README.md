# Sylius Stack

The Sylius stack is a set of tools for your Symfony projects.

📖 Documentation
----------------

Documentation is available at [stack.sylius.com](https://stack.sylius.com).

🛠️ Contributing
---------------

The repository ships a demo application to try the packages:

```bash
composer install
docker compose up -d
symfony console doctrine:schema:create
symfony console doctrine:fixtures:load -n
symfony serve -d
```

The admin panel is available at `/admin` (login: `admin@example.com` / `admin`).

### Switching the admin UI

The demo application can run with either [BootstrapAdminUi](src/BootstrapAdminUi) or [ShadcnAdminUi](src/ShadcnAdminUi):

```bash
bin/switch-admin-ui            # toggles between the two
bin/switch-admin-ui bootstrap
bin/switch-admin-ui shadcn
```

The script enables the matching bundle and theme configuration, updates the template namespaces of the demo
application, then clears the cache (and builds the Tailwind stylesheet for `shadcn`).

When working on the shadcn templates, keep the Tailwind watcher running:

```bash
symfony console tailwind:build --watch
```

🤝 Community
------------

Get Sylius support on [Slack](https://sylius.com/slack) or [Stack Overflow](https://stackoverflow.com/questions/tagged/sylius)
or get professional **SLA-backed support and Project Success Assistance** with Sylius Plus license! [Learn more.](https://sylius.com/plus/)

Stay updated by following our [Twitter](https://twitter.com/Sylius) and [Facebook](https://www.facebook.com/SyliusEcommerce/).

👮 Security issues
------------------

If you think that you have found a security issue in Sylius, please do not use the issue tracker and do not post it publicly.
Instead, all security issues must be sent to `security@sylius.com`.

📃 License
----------

Sylius Stack is completely free and released under the [MIT License](https://github.com/Sylius/Stack/blob/master/LICENSE).

✍️ Authors
---------

Sylius was originally created by the Sylius core team members.

See the full list of [contributors](https://github.com/Sylius/Stack/contributors).
