<?php

declare(strict_types=1);

namespace Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Applicator;

use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver\TaxRateResolverInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface as SyliusTaxRateResolverInterface;
use Webmozart\Assert\Assert;

final class OrderShipmentTaxesApplicator implements OrderTaxesApplicatorInterface
{
    private CalculatorInterface $calculator;
    private AdjustmentFactoryInterface $adjustmentFactory;
    private SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver;
    private TaxRateResolverInterface $highestRatBasedTaxRateResolver;

    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRateBasedTaxRateResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->shipmentMethodBasedTaxRateResolver = $shipmentMethodBasedTaxRateResolver;
        $this->highestRatBasedTaxRateResolver = $highestRateBasedTaxRateResolver;
    }

    private function addAdjustment(OrderInterface $order, int $taxAmount, string $label, bool $included): void
    {
        /** @var AdjustmentInterface $shippingTaxAdjustment */
        $shippingTaxAdjustment = $this->adjustmentFactory
            ->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, $label, $taxAmount, $included)
        ;
        $order->addAdjustment($shippingTaxAdjustment);
    }

    private function getShippingMethod(OrderInterface $order): ShippingMethodInterface
    {
        /** @var ShipmentInterface|bool $shipment */
        $shipment = $order->getShipments()->first();
        if (false === $shipment) {
            throw new \LogicException('Order should have at least one shipment.');
        }

        $method = $shipment->getMethod();

        /** @var ShippingMethodInterface $method */
        Assert::isInstanceOf($method, ShippingMethodInterface::class);

        return $method;
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $shippingTotal = $order->getShippingTotal();
        if (0 === $shippingTotal) {
            return;
        }

        $taxRate = null;
        $shippingMethod = $this->getShippingMethod($order);
        if (null === $shippingMethod->getTaxCategory()) {
            $taxRate = $this->highestRatBasedTaxRateResolver->resolve($order, $zone);
        } else {
            $taxRate = $this->shipmentMethodBasedTaxRateResolver->resolve(
                $shippingMethod,
                ['zone' => $zone]
            );
        }
        if (null === $taxRate) {
            return;
        }

        $taxAmount = $this->calculator->calculate($shippingTotal, $taxRate);
        if (0.00 === $taxAmount) {
            return;
        }

        $this->addAdjustment($order, (int) $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
    }
}
