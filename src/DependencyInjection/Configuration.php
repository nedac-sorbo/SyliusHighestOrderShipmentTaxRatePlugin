<?php

declare(strict_types=1);

namespace Nedac\SyliusHighestOrderShipmentTaxRatePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nedac_sylius_highest_order_shipment_tax_rate_plugin');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('nedac_sylius_highest_order_shipment_tax_rate_plugin');
        }

        return $treeBuilder;
    }
}
