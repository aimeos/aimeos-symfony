<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

# Aimeos Symfony bundle

[![Total Downloads](https://poser.pugx.org/aimeos/aimeos-symfony/d/total.svg)](https://packagist.org/packages/aimeos/aimeos-symfony)
[![Build Status](https://travis-ci.org/aimeos/aimeos-symfony.svg?branch=master)](https://travis-ci.org/aimeos/aimeos-symfony)
[![Coverage Status](https://coveralls.io/repos/aimeos/aimeos-symfony/badge.svg?branch=master)](https://coveralls.io/r/aimeos/aimeos-symfony?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/?branch=master)

:star: Star us on GitHub — it helps!

[Aimeos](https://aimeos.org/Symfony) is THE professional, full-featured and
ultra fast e-commerce package for Symfony!  You can install it in your existing
Symfony application within 5 minutes and can adapt, extend, overwrite and
customize anything to your needs.

[![Aimeos Symfony demo](https://aimeos.org/fileadmin/aimeos.org/images/aimeos-github.png)](http://symfony.demo.aimeos.org/)

## Table of content

- [Installation](#installation)
  - [Symfony 3](#symfony-3)
  - [Symfony 4](#symfony-4)
- [Composer](#composer)
- [Setup](#setup)
- [Admin](#admin)
- [Hints](#hints)
- [License](#license)
- [Links](#links)

## Installation

This document is for the latest Aimeos Symfony **2020.10 release and later**.

- LTS release: 2020.10 (Symfony 3.4 and 4.x)
- Beta release: 2021.01 (Symfony 3.4 and 4.x)

If you want to **upgrade between major versions**, please have a look into the [upgrade guide](https://aimeos.org/docs/Symfony/Upgrade)!

### Symfony 3

The Aimeos Symfony e-commerce bundle is a composer based library that can be installed
easiest by using [Composer](https://getcomposer.org). If you don't have an existing
Symfony application, you can create a skeleton application using

`composer create-project symfony/framework-standard-edition myshop`

You need to adapt some files inside the newly created directory. Before, the Aimeos
bundle class must be known by the `registerBundles()` method in the `app/AppKernel.php`
file so the composer post install/update scripts won't fail:

```php
    $bundles = array(
        new Aimeos\ShopBundle\AimeosShopBundle(),
        new FOS\UserBundle\FOSUserBundle(),
        ...
    );
```

Ensure that Twig is configured for templating in the `framework` section of your `./app/config/config.yml` file:

```yaml
framework:
    templating:
        engines: ['twig']
```

These settings need to be added at the end of your `./app/config/config.yml` file:

```yaml
fos_user:
    db_driver: orm
    user_class: Aimeos\ShopBundle\Entity\FosUser
    firewall_name: aimeos_myaccount
    from_email:
        address: "me@example.com"
        sender_name: "Test shop"
```

The Aimeos components have to be configured as well to get authentication working correctly.
You need to take care of two things: Using the correct customer manager implementation and
password encryption method. Both must be appended at the end of your `./app/config/config.yml`
as well as the base URL to see your uploaded images:

```yaml
aimeos_shop:
    resource:
        fs:
            baseurl: "https://yourdomain.com/"
    mshop:
        customer:
            manager:
                name: FosUser
                password:
                    name: Bcrypt
```

Make sure that the database is set up and it is configured in your `./app/config/config.yml`:

```yaml
parameters:
    database_host: <your host/ip>
    database_port: <your port>
    database_name: <your database>
    database_user: <db username>
    database_password: <db password>
```

If you want to use a database server other than MySQL, please have a look into the article about
[supported database servers](https://aimeos.org/docs/latest/infrastructure/databases/)
and their specific configuration.

### Symfony 4

The Aimeos Symfony e-commerce bundle is a composer based library that can be installed
easiest by using [Composer](https://getcomposer.org). If you don't have an existing
Symfony application, you can create a skeleton application using

`composer create-project symfony/website-skeleton myshop`

Ensure that Twig is configured for templating in the `framework` section of your
`./config/packages/framework.yaml` file:

```yaml
framework:
    templating:
        engines: ['twig']
```

These settings need to be added to the `./config/packages/fos_user.yaml` file:

```yaml
fos_user:
    db_driver: orm
    user_class: Aimeos\ShopBundle\Entity\FosUser
    firewall_name: aimeos_myaccount
    from_email:
        address: "me@example.com"
        sender_name: "Test shop"
```

The Aimeos components have to be configured as well to get authentication working correctly.
You need to take care of three things: Using the correct customer manager implementation and
password encryption method as well as the right path for the storages. All must be appended
at the end of the `./config/packages/aimeos_shop.yaml`:

```yaml
aimeos_shop:
    resource:
        fs:
            baseurl: "https://yourdomain.com/"
            basedir: "%kernel.root_dir%/../public"
        fs-admin:
            basedir: "%kernel.root_dir%/../public/uploads"
    mshop:
        customer:
            manager:
                name: FosUser
                password:
                    name: Bcrypt
```

To configure the Aimeos routing, create the file `./config/routes/aimeos_shop.yaml` with these lines:

```yaml
aimeos_shop:
    resource: "@AimeosShopBundle/Resources/config/routing.yml"
```

The same applies for the FosUser bundle. Create the file `./config/routes/fos_user.yaml` containing:

```yaml
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"
```

Make sure that the database is set up and it is configured in your `./config/packages/doctrine.yaml`:

```yaml
parameters:
    env(DATABASE_URL): ''
    database_host: <your host/ip>
    database_port: <your port>
    database_name: <your database>
    database_user: <db username>
    database_password: <db password>
```

Also, you have to configure your database credentials in the `.env` file:

`DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name`

If you want to use a database server other than MySQL, please have a look into the article about
[supported database servers](https://aimeos.org/docs/latest/infrastructure/databases/)
and their specific configuration.

Symfony 4 uses an in-memory mail spooler by default which collects the e-mails and send them
at the end. This can be problematic if there's an error because you e.g. forgot to add a
sender address and all e-mail gets lost. The settings for sending e-mails immediately in
`./config/packages/swiftmailer.yaml` are:

```yaml
swiftmailer:
    url: '%env(MAILER_URL)%'
    sender_address: <your@domain.com>
#    spool: { type: 'memory' }
```

If you don't use Sendmail but SMTP for sending e-mails, you have to adapt the `MAILER_URL`
configuration in your `.env` file, e.g.:

`MAILER_URL=smtp://smtp.mailtrap.io:2525?encryption=tls&auth_mode=login&username=...&password=...`

## Composer

Then add these lines to your `composer.json` of your Symfony project:

```json
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-symfony": "~2020.10",
        ...
    },
    "scripts": {
        "post-install-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::updateConfig",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::setupDatabase",
            ...
        ],
        "post-update-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::updateConfig",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::setupDatabase",
            ...
        ]
    }
```

Afterwards, install the Aimeos shop bundle using

`composer update`

In a production environment or if you don't want that the demo data gets installed,
use the --no-dev option:

`SYMFONY_ENV=prod composer update --no-dev`

If you get an exception that the `SensioGeneratorBundle` isn't found, follow the
steps described in the
[Aimeos Symfony forum post](https://aimeos.org/help/symfony-bundle-f17/symfony-env-prod-composer-update-no-dev-t1488.html#p6384)

## Setup

To see all components and get everything working, you also need to adapt your
Twig base template. This is a working example using the
[Twitter bootstrap CSS framework](http://getbootstrap.com/) and you need to replace
the existing file with the content below:

- Symfony 3: `./app/Resources/views/base.html.twig`
- Symfony 4: `./templates/base.html.twig`

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {% block aimeos_header %}{% endblock %}
        <title>{% block title %}Aimeos shop{% endblock %}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/css/bootstrap.min.css">
        <style>
            /* Theme: Black&White */
            /* body {
                --ai-primary: #000; --ai-primary-light: #000; --ai-primary-alt: #fff;
                --ai-bg: #fff; --ai-bg-light: #fff; --ai-bg-alt: #000;
                --ai-secondary: #555; --ai-light: #D0D0D0;
            } */
            body { color: #000; color: var(--ai-primary, #000); background-color: #fff; background-color: var(--ai-bg, #fff); }
            .navbar, footer { color: #555; color: var(--ai-primary-alt, #555); background-color: #f8f8f8; background-color: var(--ai-bg-alt, #f8f8f8); }
            .navbar a, .navbar a:before, .navbar span, footer a { color: #555 !important; color: var(--ai-primary-alt, #555) !important; }
            .content { margin: 0 5% } .catalog-stage-image { margin: 0 -5.55% }
            .sm:before { font: normal normal normal 14px/1 FontAwesome; padding: 0 0.2em; font-size: 225% }
            .facebook:before { content: "\f082" } .twitter:before { content: "\f081" } .instagram:before { content: "\f16d" } .youtube:before { content: "\f167" }
        </style>
        {% block aimeos_styles %}{% endblock %}
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-light">
            <a class="navbar-brand" href="/">
                <img src="http://aimeos.org/fileadmin/template/icons/logo.png" height="30" title="Aimeos Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                {% block aimeos_head %}{% endblock %}
            </div>
        </nav>
        <div class="container">
            {% block aimeos_nav %}{% endblock %}
            {% block aimeos_stage %}{% endblock %}
            {% block aimeos_body %}{% endblock %}
            {% block aimeos_aside %}{% endblock %}
        </div>
        <footer class="mt-5 p-5">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-6 my-4"><h2>LEGAL</h2><p><a href="#">Terms & Conditions</a></p><p><a href="#">Privacy Notice</a></p><p><a href="#">Imprint</a></p></div>
                        <div class="col-sm-6 my-4"><h2>ABOUT US</h2><p><a href="#">Contact us</a></p><p><a href="#">Company</a></p></div>
                    </div>
                </div>
                <div class="col-md-4 my-4">
                    <div class="social"><a href="#" class="sm facebook"></a><a href="#" class="sm twitter"></a><a href="#" class="sm instagram"></a><a href="#" class="sm youtube"></a></div>
                    <a class="px-2 py-4 d-inline-block" href="/"><img src="http://aimeos.org/fileadmin/template/icons/logo.png" style="width: 160px" title="Aimeos Logo"></a>
                </div>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/combine/npm/jquery@3,npm/bootstrap@4"></script>
        {% block aimeos_scripts %}{% endblock %}
    </body>
</html>
```

Start the PHP web server in the base directory of your application to do some quick tests:

```php -S 127.0.0.1:8000 -t public```

Then, you should be able to call the catalog list page in your browser using

```http://127.0.0.1:8000/shop```


## Login and Admin

Setting up the administration interface is a matter of configuring the Symfony
firewall to restrict access to the admin URLs. Since 2017.07, the FOSUserBundle
is required.

Setting up the security configuration is the most complex part. The firewall
setup should look like this one:

- Symfony 3: `./app/config/security.yml`
- Symfony 4: `./config/packages/security.yaml`

```yaml
security:
    providers:
        aimeos:
            entity: { class: AimeosShopBundle:FosUser, property: username }

    encoders:
        Aimeos\ShopBundle\Entity\FosUser: bcrypt

    firewalls:
        aimeos_admin:
            pattern:   ^/admin
            anonymous: ~
            provider: aimeos
            logout_on_user_change: true
            form_login:
                login_path: /admin
                check_path: /admin_check
        aimeos_myaccount:
            pattern: ^/
            form_login:
                provider: aimeos
                csrf_token_generator: security.csrf.token_manager
            logout:       true
            anonymous:    true

    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/profile, roles: ROLE_USER }
        - { path: ^/admin/.+, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
```

**Caution:** The order of the configuration settings in this file is important!

These settings will protect the ```/admin/*``` URLs from unauthorized access from
someone without admin privileges.

The `/profile` URL is protected by the FOS user bundle as well, which also offers
user registration.

As last step, you have to create an admin account using the Symfony command line:

```bash
./bin/console aimeos:account --admin me@mydomain.com
```

The e-mail address is the user name for login and the account will work for the frontend too.
To protect the new account, the command will ask you for a password. The same command can
create limited accounts by using "--editor" instead of "--admin". If you use "--super" the
account will have access to all sites.

If the PHP web server is still running (`php -S 127.0.0.1:8000 -t public`), you should be
able to call the admin login page in your browser using

```http://127.0.0.1:8000/admin```

and authenticating with your e-mail and the password which has been asked for by the
`aimeos:account` command.

## Hints

To simplify development, you should configure to use no content cache. You can
do this by adding these lines to:

- Symfony 3: `./app/config/config.yml`
- Symfony 4: `./config/packages/aimeos_shop.yaml`

```yaml
aimeos_shop:
    madmin:
        cache:
            manager:
                name: None
```

## License

The Aimeos Symfony bundle is licensed under the terms of the MIT license and is available for free.

## Links

* [Web site](https://aimeos.org/Symfony)
* [Documentation](https://aimeos.org/docs/latest/symfony/)
* [Forum](https://aimeos.org/help/symfony-bundle-f17/)
* [Issue tracker](https://github.com/aimeos/aimeos-symfony/issues)
* [Composer packages](https://packagist.org/packages/aimeos/aimeos-symfony)
* [Source code](https://github.com/aimeos/aimeos-symfony)
