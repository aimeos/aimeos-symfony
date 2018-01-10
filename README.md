<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos Symfony bundle
======================
[![Build Status](https://travis-ci.org/aimeos/aimeos-symfony.svg?branch=master)](https://travis-ci.org/aimeos/aimeos-symfony)
[![Coverage Status](https://coveralls.io/repos/aimeos/aimeos-symfony/badge.svg?branch=master)](https://coveralls.io/r/aimeos/aimeos-symfony?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/?branch=master)

The repository contains the Symfony e-commerce bundle integrating the Aimeos e-commerce
library into Symfony 2 and 3. The bundle provides controllers for e.g. faceted filter,
product lists and detail views, for searching products as well as baskets and the
checkout process. A full set of pages including routing is also available for a quick start.

[![Aimeos Symfony demo](https://aimeos.org/fileadmin/user_upload/symfony-demo.jpg)](http://symfony.demo.aimeos.org/)

## Table of content

- [Installation](#installation)
- [Setup](#setup)
- [Admin](#admin)
- [Hints](#hints)
- [License](#license)
- [Links](#links)

## Installation

This document is for the latest Aimeos Symfony **2017.10 release and later**.

- Beta release: 2018.01
- LTS release: 2017.10

If you want to **upgrade between major versions**, please have a look into the [upgrade guide](https://aimeos.org/docs/Symfony/Upgrade)!

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

Ensure that Twig is configured for templating in the `framework` section:

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
as well:

```yaml
aimeos_shop:
    mshop:
        customer:
            manager:
                name: FosUser
                password:
                    name: Bcrypt
```

Make sure that the database is set up and it is configured in your config.yml.
If you want to use a database server other than MySQL, please have a look into the article about
[supported database servers](https://aimeos.org/docs/Developers/Library/Database_support)
and their specific configuration.

Then add these lines to your composer.json of your Symfony project:

```
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-symfony": "~2017.10",
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

## Setup

To see all components and get everything working, you also need to adapt your
Twig base template in `app/Resources/views/base.html.twig`. This is a working
example using the [Twitter bootstrap CSS framework](http://getbootstrap.com/):

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

```php -S 127.0.0.1:8000 -t web```

Then, you should be able to call the catalog list page in your browser using

```http://127.0.0.1:8000/app_dev.php/list ```

## Login and Admin

Setting up the administration interface is a matter of configuring the Symfony
firewall to restrict access to the admin URLs. Since 2017.07, the FOSUserBundle
is required. For a more detailed description, please read the article about
[setting up the FOSUserBundle](https://aimeos.org/docs/Symfony/Configure_FOSUserBundle_login).

To add the required routes for the FOSUserBundle, append these two lines at the
end of your `./app/config/routing.yml` file:

```yaml
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"
```

Setting up the security configuration is the most complex part. The firewall
setup in the `./app/config/security.yml` file should look like this one:

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
        - { path: ^/admin/.+, roles: ROLE_ADMIN }
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
./bin/console fos:user:promote me@mydomain.com ROLE_ADMIN
```

Please replace `me@mydomain.com` with your own e-mail address. If the PHP web server is
still running (`php -S 127.0.0.1:8000 -t web`), you should be able to call the admin
login page in your browser using:

`http://127.0.0.1:8000/app_dev.php/admin`

and authenticating with your e-mail and the password which has been asked for by the
aimeos:account command.

## Hints

To simplify development, you should configure to use no content cache. You can
do this in the ./app/config/config_dev.yml file of your Symfony application by
adding these lines:
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
