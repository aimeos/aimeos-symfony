<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos Symfony bundle
======================

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

This document is for the latest Aimeos Symfony **2018.10 release and later**.

- Stable release: 2019.07
- LTS release: 2018.10

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
[supported database servers](https://aimeos.org/docs/Developers/Library/Database_support)
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
[supported database servers](https://aimeos.org/docs/Developers/Library/Database_support)
and their specific configuration.

## Composer

Then add these lines to your `composer.json` of your Symfony project:

```json
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-symfony": "~2014.07",
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
        <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
{% block aimeos_styles %}{% endblock %}
    </head>
    <body>
        <div class="navbar navbar-static" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
          </div>
{% block aimeos_head %}{% endblock %}
        </div>
        <div class="col-xs-12">
            {% block aimeos_nav %}{% endblock %}
            {% block aimeos_stage %}{% endblock %}
            {% block aimeos_body %}{% endblock %}
            {% block aimeos_aside %}{% endblock %}
        </div>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{% block aimeos_scripts %}{% endblock %}
    </body>
</html>
```

Start the PHP web server in the base directory of your application to do some quick tests:

```./bin/console server:run```

Then, you should be able to call the catalog list page in your browser using

Since 2019.04: http://127.0.0.1:8000/shop
Until 2019.01: http://127.0.0.1:8000/list


## Login and Admin

Setting up the administration interface is a matter of configuring the Symfony
firewall to restrict access to the admin URLs. Since 2017.07, the FOSUserBundle
is required. For a more detailed description, please read the article about
[setting up the FOSUserBundle](https://aimeos.org/docs/Symfony/Configure_FOSUserBundle_login).

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
        - { path: ^/myaccount, roles: ROLE_USER }
        - { path: ^/admin/.+, roles: [ROLE_ADMIN, ROLE_SUPER_ADMIN] }
```

**Caution:** The order of the configuration settings in this file is important!

These settings will protect the ```/admin/*``` URLs from unauthorized access from
someone without admin privileges.

The `/myaccount` URL is protected by the FOS user bundle as well, which also offers
user registration. A bit more detailed explanation of the authentication is available in the
[Aimeos docs](https://aimeos.org/docs/Symfony/Configure_FOSUserBundle_login).

As last step, you have to create an admin account using the Symfony command line:

```bash
./bin/console aimeos:account --admin me@mydomain.com
```

The e-mail address is the user name for login and the account will work for the frontend too.
To protect the new account, the command will ask you for a password. The same command can
create limited accounts by using "--editor" instead of "--admin". If you use "--super" the
account will have access to all sites.

If the PHP web server is still running (`./bin/console server:run`), you should be
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
* [Documentation](https://aimeos.org/docs/Symfony)
* [Forum](https://aimeos.org/help/symfony-bundle-f17/)
* [Issue tracker](https://github.com/aimeos/aimeos-symfony/issues)
* [Composer packages](https://packagist.org/packages/aimeos/aimeos-symfony)
* [Source code](https://github.com/aimeos/aimeos-symfony)
