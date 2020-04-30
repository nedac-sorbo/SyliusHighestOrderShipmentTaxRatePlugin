<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\DependencyInjection\NedacSyliusHighestOrderShipmentTaxRateExtension;
use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Applicator\OrderShipmentTaxesApplicator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class NedacSyliusHighestOrderShipmentTaxRateExtensionTest extends AbstractExtensionTestCase
{
    private const SERVICES = [
        'nedac.sylius_highest_order_shipment_tax_rate_plugin.taxation.resolver.tax_rate',
        'Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver\TaxRateResolverInterface',
        'nedac.sylius_highest_order_shipment_tax_rate_plugin.taxation.applicator.order_shipment_taxes',
        'sylius.taxation.order_shipment_taxes_applicator'
    ];

    /**
     * @return array<int, ExtensionInterface>
     */
    protected function getContainerExtensions(): array
    {
        return [new NedacSyliusHighestOrderShipmentTaxRateExtension()];
    }

    public function testHasOurServices(): void
    {
        $this->load();

        foreach (self::SERVICES as $id) {
            $this->assertContainerBuilderHasService($id);
        }
    }

    public function testTheSyliusServiceIsOverridden(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(
            'sylius.taxation.order_shipment_taxes_applicator',
            OrderShipmentTaxesApplicator::class
        );
    }
}
