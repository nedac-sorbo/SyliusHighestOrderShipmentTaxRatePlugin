<div class="repo-badge inline-block vertical-align">
    <a id="status-image-popup" title="Latest push build on default branch: started" name="status-images" class="pointer open-popup">
        <img src="https://travis-ci.com/nedac-sorbo/SyliusHighestOrderShipmentTaxRatePlugin.svg?branch=master" alt="build:started">
    </a>
</div>
<br />
This plugin will modify the way taxes are applied to the shipping costs. By default Sylius will apply the taxes from
the first matching tax rate in the assigned tax category of the shipping method or will apply no taxes when no tax
category is assigned.

By installing this plugin Sylius will still use the assigned tax category if it is assigned to the shipping method.
However if no tax category is assigned to the shipping method it will use the highest applicable tax rate that will be
applied on the order items in the order.

Doing so makes the application of taxes on the shipping costs comply with the laws of for example the Dutch "BTW" system
and likely that of other countries within the European Union as well.

##### Supported Sylius versions:
<table>
    <tr><td>1.6</td></tr>
</table>


> **_NOTE:_** *This plugin requires PHP 7.4 or up*

#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-highest-order-shipment-tax-rate-plugin
    ```
2. Add to bundles.php:
    ```php
    # config/bundles.php
    <?php

    return [
        # ...
        Nedac\SyliusHighestOrderShipmentTaxRatePlugin\NedacSyliusHighestOrderShipmentTaxRatePlugin::class => ['all' => true],
    ];
    ```
3. Clear cache:
    ```bash
    bin/console cache:clear
    ```
#### Setup development environment:
```bash
docker-compose build
docker-compose up -d
docker-compose exec php composer --working-dir=/srv/sylius install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application build
docker-compose exec php bin/console assets:install public
docker-compose exec php bin/console doctrine:schema:create
docker-compose exec php bin/console sylius:fixtures:load -n
```
#### Running tests:
```bash
docker-compose exec php sh
bin/console doc:sche:cre
cd ../..
vendor/bin/phpcs
vendor/bin/phpstan analyse src/ --level max
vendor/bin/phpspec run
vendor/bin/phpunit
vendor/bin/behat
```
