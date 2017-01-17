<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos Symfony bundle
======================
[![Build Status](https://travis-ci.org/aimeos/aimeos-symfony.svg?branch=master)](https://travis-ci.org/aimeos/aimeos-symfony)
[![Coverage Status](https://coveralls.io/repos/aimeos/aimeos-symfony/badge.svg?branch=master)](https://coveralls.io/r/aimeos/aimeos-symfony?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/aimeos/aimeos-symfony.svg)](http://hhvm.h4cc.de/package/aimeos/aimeos-symfony)

The repository contains the Symfony e-commerce bundle integrating the Aimeos e-commerce
library into Symfony 2 and 3. The bundle provides controllers for e.g. faceted filter,
product lists and detail views, for searching products as well as baskets and the
checkout process. A full set of pages including routing is also available for a quick start.

[![Aimeos Symfony2 demo](https://aimeos.org/fileadmin/user_upload/symfony-demo.jpg)](http://symfony2.demo.aimeos.org/)

## Table of content

- [Installation](#installation)
- [Setup](#setup)
- [Admin](#admin)
- [Hints](#hints)
- [License](#license)
- [Links](#links)

## Installation

This document is for the latest Aimeos Symfony **2016.10 release and later**.

- Beta release: 2017.01
- LTS release: 2016.10

If you want to **upgrade between major versions**, please have a look into the [upgrade guide](https://aimeos.org/docs/Symfony/Upgrade)!

The Aimeos Symfony e-commerce bundle is a composer based library that can be installed
easiest by using [Composer](https://getcomposer.org). Before, the Aimeos bundle class
must be known by the `registerBundles()` method in the `app/AppKernel.php` file so the
composer post install/update scripts won't fail:

```php
    $bundles = array(
        new Aimeos\ShopBundle\AimeosShopBundle(),
        ...
    );
```

Make sure that the database is set up and it is configured in your config.yml. Then add these lines to your composer.json of your Symfony2 project:

```
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-symfony": "~2016.10",
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

## Admin

Setting up the administration interface is a matter of configuring the Symfony
firewall to restrict access to the admin URLs. A basic firewall setup in the
`./app/config/security.yml` file can look like this one:

```yaml
security:
    providers:
        admin:
            memory:
                users:
                    admin: { password: secret, roles: [ 'ROLE_ADMIN' ] }
        aimeos_customer:
            entity: { class: AimeosShopBundle:User, property: username }
        in_memory:
            memory: ~

    encoders:
        Symfony\Component\Security\Core\User\User: plaintext
        Aimeos\ShopBundle\Entity\User:
            algorithm: sha1
            encode_as_base64: false
            iterations: 1


    firewalls:
        aimeos_admin:
            pattern:   ^/(admin|extadm|jqadm|jsonadm)
            anonymous: ~
            provider: admin
            form_login:
                login_path: /admin
                check_path: /admin_check
        aimeos_myaccount:
            pattern: ^/myaccount
            provider: aimeos_customer
            http_basic:
                realm: "MyAccount"
        main:
            anonymous: ~

    access_control:
        - { path: ^/(extadm|jqadm|jsonadm), roles: ROLE_ADMIN }
        - { path: ^/myaccount, roles: ROLE_USER }
```

**Caution:** The order of the configuration settings in this file is important!
If you place the `in_memory` or `main` section before the Aimeos related sections,
authentication will fail!

These settings will protect the ```/extadm``` (ExtJS), the ```/jqadm``` (JQuery+Bootstrap)
and ```/jsonadm``` (JSON API) URLs from unauthorized access from someone without
admin privileges. There's only one user/password combination defined, which is
rather inflexible. As alternative, you can use on of the other Symfony user provider
to authenticate against.

The `/myaccount` URL is protected by HTTP basic authentication in this short
example. Usually, you will replace it with a form based login or use the FOS user
bundle with also offers user registration. A bit more detailed explanation of the
authentication is available in the
[Aimeos docs](https://aimeos.org/docs/Symfony/Configure_admin_myaccount_login)
and it contains the
[setup of the FOS user bundle](https://aimeos.org/docs/Symfony/Configure_FOSUserBundle_login) too.

**Caution:** This is only an example and contains a public password! Use **a strong
password for authentication** in production environments!

If the PHP web server is still running (`php -S 127.0.0.1:8000 -t web`), you should be
able to call the admin login page in your browser using:

`http://127.0.0.1:8000/app_dev.php/admin`

and authenticating with the user `admin` and the password `secret` as defined in your
`./app/config/security.yml` file.

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
