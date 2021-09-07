This plugin will modify the way that taxes are applied to the shipping costs. By default Sylius will apply the taxes from
the first matching tax rate in the assigned tax category of the shipping method or will apply no taxes when no tax
category is assigned.

By installing this plugin Sylius will still use the assigned tax category if it is assigned to the shipping method.
However if no tax category is assigned to the shipping method it will use the highest applicable tax rate that is
applied on the order items in the order.

Doing so makes the application of taxes on the shipping costs comply with the laws of for example the Dutch "BTW" system
and likely that of other countries within the European Union as well.

##### Supported Sylius versions:
<table>
    <tr><td>1.10</td></tr>
</table>


> **_NOTE:_** *This plugin requires PHP 7.4 or up*

#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-highest-order-shipment-tax-rate-plugin
    ```
2. Clear cache:
    ```bash
    bin/console cache:clear
    ```
