symfony2-bundle
===============

Aimeos Symfony2 web shop bundle

## Installation

Add the main bundle class to the `registerBundles()` method in the `app/AppKernel.php` file:

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
        "url": "https://github.com/nsendetzky/arcavias-core"
    } ],
    "minimum-stability": "dev",
    "require": {
        "aimeos/symfony2-bundle": "dev-master",
        ...
    },
    "scripts": {
        "post-install-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::updateDatabase",
            ...
        ],
        "post-update-cmd": [
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::installBundle",
            "Aimeos\\ShopBundle\\Composer\\ScriptHandler::updateDatabase",
            ...
        ]
    }
```

Afterwards, install the Aimeos shop bundle using `composer update`

## Usage

If the setup was successfull and the tables were created in the database, you should be able to call the catalog list page in your browser using

```http://<your web root>/app.php/list```
