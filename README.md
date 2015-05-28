<a href="http://aimeos.org/">
    <img src="http://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos Symfony2 bundle
======================
[![Build Status](https://travis-ci.org/aimeos/aimeos-symfony2.svg?branch=master)](https://travis-ci.org/aimeos/aimeos-symfony2)
[![Coverage Status](https://coveralls.io/repos/aimeos/aimeos-symfony2/badge.svg?branch=master)](https://coveralls.io/r/aimeos/aimeos-symfony2?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony2/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/aimeos/aimeos-symfony2.svg)](http://hhvm.h4cc.de/package/aimeos/aimeos-symfony2)

The repository contains the Symfony2 web shop bundle integrating the Aimeos e-commerce library into Symfony. The bundle provides controllers for e.g. faceted filter, product lists and detail views, for searching products as well as baskets and the checkout process. A full set of pages including routing is also available for a quick start.

[![Aimeos Symfony2 demo](http://aimeos.org/fileadmin/user_upload/symfony-demo.jpg)](http://symfony2.demo.aimeos.org/)

## Table of content

- [Installation](#installation)
- [Setup](#setup)
- [Hints](#hints)
- [License](#license)
- [Links](#links)

## Installation

The Aimeos Symfony2 web shop bundle is a composer based library that can be installed easiest by using [Composer](https://getcomposer.org). Before, the Aimeos bundle class must be known by the `registerBundles()` method in the `app/AppKernel.php` file so the composer post install/update scripts won't fail:

```
    $bundles = array(
        new Aimeos\ShopBundle\AimeosShopBundle(),
        ...
    );
```

Make sure that the database is set up and it is configured in your config.yml. Then add these lines to your composer.json of your Symfony2 project:

```
    "repositories": [ {
        "type": "vcs",
        "url": "https://github.com/aimeos/arcavias-core"
    } ],
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "aimeos/aimeos-symfony2": "dev-master",
        ...
    },
    "scripts": {
        "post-install-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::setupDatabase",
            ...
        ],
        "post-update-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::setupDatabase",
            ...
        ]
    }
```

Afterwards, install the Aimeos shop bundle using

`composer update`

In a production environment or if you don't want that the demo data gets installed, use the --no-dev option:

`composer update --no-dev`

**Note:** Alternatively to running the `post-install-cmd` and `post-update-cmd` scripts automatically, you can add the lines required for installing the bundle manually. In your `./app/config/config.yml` file you need to add "AimeosShopBundle" to the list of bundles managed by the assetic bundle:
```
assetic:
    # ...
    bundles:        ['AimeosShopBundle']
```

Furthermore, add the Aimeos routes to your ```./app/config/routing.yml```
```
aimeos_shop:
    resource: "@AimeosShopBundle/Resources/config/routing.yml"
    prefix: /
```

For setting up the database, please run the following commands afterwards: 
```
php app/console aimeos:setup
php app/console aimeos:cache
```

Finally, create the ```./web/uploads/``` directory and make sure it's writeable by the web server:
```
mkdir ./web/uploads/
chmod 777 ./web/uploads/
```
In your production environment, you should use these commands as root instead:
```
mkdir ./web/uploads/
chmod 755 ./web/uploads/
chown www-data:www-data ./web/uploads/
```


## Setup

To see all components and get everything working, you also need to adapt your Twig base template in `app/Resources/views/base.html.twig`. This is a working example using the [Twitter bootstrap CSS framework](http://getbootstrap.com/):

```
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
{% block aimeos_header %}{% endblock %}
        <title>{% block title %}Aimeos shop{% endblock %}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
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
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
{% block aimeos_scripts %}{% endblock %}
    </body>
</html>
```

Then, you should be able to call the catalog list page in your browser using

```http://<your web root>/app_dev.php/list```

## Hints

To simplify development, you should configure to use no content cache. You can do this in the ./app/config/config_dev.yml file of your Symfony application by adding these lines:

```
aimeos_shop:
    classes:
        cache:
            manager:
                name: None
```

## License

The Aimeos Symfony2 bundle is licensed under the terms of the MIT license and is available for free.

## Links

* [Web site](http://aimeos.org/app/symfony-shop-bundle/)
* [Documentation](http://docs.aimeos.org/Symfony)
* [Help](http://help.aimeos.org/)
* [Issue tracker](https://github.com/aimeos/aimeos-symfony2/issues)
* [Source code](https://github.com/aimeos/aimeos-symfony2)
