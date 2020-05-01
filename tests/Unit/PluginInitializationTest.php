<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Unit;

use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use PHPUnit\Framework\TestCase;
use Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Application\Kernel;

final class PluginInitializationTest extends TestCase
{
    private const SERVICES = [
        'nedac.sylius_highest_order_shipment_tax_rate_plugin.taxation.resolver.tax_rate',
        'nedac.sylius_highest_order_shipment_tax_rate_plugin.taxation.applicator.order_shipment_taxes'
    ];

    public function testInitPlugin(): void
    {
        $kernel = new Kernel('test', true);
        $kernel->addCompilerPass(new PublicServicePass('|nedac.*|'));
        $kernel->boot();

        $container = $kernel->getContainer();
        foreach (self::SERVICES as $id) {
            $this->assertTrue($container->has($id), $id);
        }
    }
}
