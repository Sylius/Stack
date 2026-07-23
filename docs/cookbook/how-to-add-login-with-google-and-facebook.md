# How to add login with Google and Facebook

This guide shows how to let your customers sign in to the Sylius shop with their **Google** or **Facebook** account, using [HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle) to handle the OAuth 2.0 / OpenID Connect flow.

{% hint style="info" %}
Reference documentation used throughout this guide:

* [HWIOAuthBundle documentation](https://github.com/hwi/HWIOAuthBundle/tree/master/docs) — especially [Configuring resource owners](https://github.com/hwi/HWIOAuthBundle/blob/master/docs/2-configuring_resource_owners.md) and [Configuring the security layer](https://github.com/hwi/HWIOAuthBundle/blob/master/docs/3-configuring_the_security_layer.md)
* [Facebook Login documentation](https://developers.facebook.com/documentation/facebook-login)
* [Google OpenID Connect documentation](https://developers.google.com/identity/openid-connect/openid-connect)
{% endhint %}

## Install the bundle

```shell
composer require hwi/oauth-bundle php-http/httplug-bundle
```

Enable both bundles:

{% code title="config/bundles.php" %}
```php
return [
    // ...
    Http\HttplugBundle\HttplugBundle::class => ['all' => true],
    HWI\Bundle\OAuthBundle\HWIOAuthBundle::class => ['all' => true],
];
```
{% endcode %}

## Configure the HTTP client

HWIOAuthBundle talks to the providers through an HTTPlug client:

{% code title="config/packages/httplug.yaml" %}
```yaml
httplug:
    plugins:
        retry:
            retry: 1
    discovery:
        client: 'auto'
    clients:
        app:
            http_methods_client: true
            plugins:
                - 'httplug.plugin.content_length'
                - 'httplug.plugin.redirect'
```
{% endcode %}

## Configure the resource owners

Declare one resource owner per provider. Point HWIOAuthBundle at the `shop` firewall.

{% code title="config/packages/hwi_oauth.yaml" %}
```yaml
hwi_oauth:
    firewall_names: [shop]
    resource_owners:
        facebook:
            type: facebook
            client_id: <client_id>
            client_secret: <client_secret>
            scope: "email"
        google:
            type: google
            client_id: <client_id>
            client_secret: <client_secret>
            scope: "openid email profile"
```
{% endcode %}

## Configure the routes

Import HWIOAuthBundle's routing and — importantly — declare a **check route** for every provider.

{% code title="config/routes.yaml" %}
```yaml
hwi_oauth_redirect:
    resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
    prefix: /connect

hwi_oauth_connect:
    resource: "@HWIOAuthBundle/Resources/config/routing/connect.xml"
    prefix: /connect

hwi_oauth_login:
    resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
    prefix: /login

facebook_login:
    path: /login/check-facebook

google_login:
    path: /login/check-google
```
{% endcode %}

## Configure the security firewall

Add an `oauth` section to the `shop` firewall and map each resource owner to its check path.

{% code title="config/packages/security.yaml" %}
```yaml
security:
    firewalls:
        shop:
            # ...
            entry_point: form_login
            oauth:
                resource_owners:
                    facebook: "/login/check-facebook"
                    google: "/login/check-google"
                login_path: sylius_shop_login
                use_forward: false
                failure_path: sylius_shop_login
                oauth_user_provider:
                    service: sylius.oauth.user_provider
```
{% endcode %}

{% hint style="info" %}
Sylius ships the `sylius.oauth.user_provider` service, which finds or creates the shop user from the provider response (matched by e-mail).
{% endhint %}

## Add the login buttons

Sylius renders the login page through Twig Hooks. Add one small template per provider and register it in the `login_container` hook — no need to override the whole login template.

{% code title="templates/bundles/shop/account/login/facebook.html.twig" %}
```twig
<a href="{{ path('hwi_oauth_service_redirect', {'service': 'facebook' }) }}">
    <span>Login with Facebook</span>
</a>
```
{% endcode %}

{% code title="templates/bundles/shop/account/login/google.html.twig" %}
```twig
<a href="{{ path('hwi_oauth_service_redirect', {'service': 'google' }) }}">
    <span>Login with Google</span>
</a>
```
{% endcode %}

{% code title="config/packages/_sylius_social_login.yaml" %}
```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.account.login.content.login_container':
            facebook_login:
                template: 'bundles/shop/account/login/facebook.html.twig'
                priority: 50
            google_login:
                template: 'bundles/shop/account/login/google.html.twig'
                priority: 40
```
{% endcode %}

## Register the applications on the provider side

### Facebook

1. Create an app at [developers.facebook.com](https://developers.facebook.com/apps/) and add the **Facebook Login** product.
2. In **Facebook Login → Settings → Client OAuth settings**, add your callback to **Valid OAuth Redirect URIs**: `https://your-domain/login/check-facebook`
3. In **Permissions and features**, make sure the `email` permission is added.
4. Copy **App ID** → `client_id` and **App Secret** → `client_secret`.

### Google

1. In the [Google Cloud Console](https://console.cloud.google.com), configure the **OAuth consent screen** and add your account under **Test users**.
2. Create an **OAuth client ID** (**APIs & Services → Credentials**), type **Web application**.
3. Add your callback to **Authorized redirect URIs**: `https://your-domain/login/check-google`
4. Copy the **Client ID** → `client_id` and **Client Secret** → `client_secret`.
