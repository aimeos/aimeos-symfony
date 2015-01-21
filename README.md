Aimeos Symfony2 bundle
======================
[![Build Status](https://travis-ci.org/aimeos/aimeos-symfony2.svg?branch=master)](https://travis-ci.org/aimeos/aimeos-symfony2)
[![Coverage Status](https://coveralls.io/repos/aimeos/aimeos-symfony2/badge.svg?branch=master)](https://coveralls.io/r/aimeos/aimeos-symfony2?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/aimeos-symfony2/?branch=master)

[![Aimeos Symfony2 demo](http://aimeos.org/fileadmin/user_upload/symfony-demo.jpg)](http://symfony2.demo.aimeos.org/)

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


## Usage

To see all components and get everything working, you also need to adapt your Twig base template in `app/Resources/views/base.html.twig`. This is a working example using the [Twitter bootstrap CSS framework](http://getbootstrap.com/getting-started/#download):

```
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
{% block header %}{% endblock %}
        <title>{% block title %}Aimeos shop{% endblock %}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
{% block stylesheets %}{% endblock %}
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
{% block head %}{% endblock %}
        </div>
        <div class="col-xs-12">
            {% block nav %}{% endblock %}
            {% block stage %}{% endblock %}
            {% block body %}{% endblock %}
            {% block aside %}{% endblock %}
        </div>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
{% block javascripts %}{% endblock %}
    </body>
</html>
```

Copy the

* css/
* js/
* fonts/

directories from the bootstrap .zip package into the web/ directory of your Symfony2 application. Then, you should be able to call the catalog list page in your browser using

```http://<your web root>/app_dev.php/list```

To simplify development, you should configure to use no content cache. You can do this in the ./app/config/config_dev.yml file of your Symfony application by adding these lines:

```
parameters:
    classes:
        cache:
            manager:
                name: None
```
