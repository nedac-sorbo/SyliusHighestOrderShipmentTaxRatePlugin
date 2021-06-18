<?php

declare(strict_types=1);

namespace Nedac\SyliusHighestOrderShipmentTaxRatePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('nedac_sylius_highest_order_shipment_tax_rate_plugin');
    }
}
