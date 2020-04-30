<?php

declare(strict_types=1);

namespace spec\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Applicator;

use Doctrine\Common\Collections\Collection;
use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Applicator\OrderShipmentTaxesApplicator;
use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver\TaxRateResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface as SyliusTaxRateResolverInterface;

class OrderShipmentTaxesApplicatorSpec extends ObjectBehavior
{
    public function it_is_initializable(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $this->shouldHaveType(OrderShipmentTaxesApplicator::class);
        $this->shouldHaveType(OrderTaxesApplicatorInterface::class);
    }

    public function it_does_not_apply_taxes_when_the_shipping_total_is_zero(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(0);
        $order->addAdjustment()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    public function it_throws_logic_exception_when_order_has_no_shipments(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn(false);

        $this->shouldThrow(\LogicException::class)->duringApply($order, $zone);
    }

    public function it_throws_invalid_argument_exception_when_shipment_has_no_method(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->duringApply($order, $zone);
    }

    public function it_does_not_apply_taxes_when_highest_tax_rate_cannot_be_resolved(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn(null);
        $highestRatBasedTaxRateResolver->resolve($order, $zone)->shouldBeCalledOnce()->willReturn(null);
        $order->addAdjustment()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    public function it_does_not_apply_taxes_when_shipment_method_tax_rate_cannot_be_resolved(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $shippingTaxCategory
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn($shippingTaxCategory);
        $shipmentMethodBasedTaxRateResolver
            ->resolve($shippingMethod, ['zone' => $zone])
            ->shouldBeCalledOnce()
            ->willReturn(null)
        ;
        $order->addAdjustment()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    public function it_does_not_apply_taxes_when_highest_tax_rate_can_be_resolved_but_calculations_are_zero(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn(null);
        $highestRatBasedTaxRateResolver->resolve($order, $zone)->shouldBeCalledOnce()->willReturn($taxRate);
        $calculator->calculate(1, $taxRate)->shouldBeCalledOnce()->willReturn(0.0);
        $order->addAdjustment()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    public function it_does_not_apply_taxes_when_shipment_method_tax_rate_can_be_resolved_but_calculations_are_zero(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $shippingTaxCategory,
        TaxRateInterface $taxRate
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn($shippingTaxCategory);
        $shipmentMethodBasedTaxRateResolver
            ->resolve($shippingMethod, ['zone' => $zone])
            ->shouldBeCalledOnce()
            ->willReturn($taxRate)
        ;
        $calculator->calculate(1, $taxRate)->shouldBeCalledOnce()->willReturn(0.0);
        $order->addAdjustment()->shouldNotBeCalled();

        $this->apply($order, $zone);
    }

    public function it_can_apply_taxes_when_highest_tax_rate_can_be_resolved(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $taxRate,
        AdjustmentInterface $adjustment
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn(null);
        $highestRatBasedTaxRateResolver->resolve($order, $zone)->shouldBeCalledOnce()->willReturn($taxRate);
        $calculator->calculate(1, $taxRate)->shouldBeCalledOnce()->willReturn(1.0);
        $taxRate->getLabel()->shouldBeCalledOnce()->willReturn('test');
        $taxRate->isIncludedInPrice()->shouldBeCalledOnce()->willReturn(false);
        $adjustmentFactory
            ->createWithData(
            AdjustmentInterface::TAX_ADJUSTMENT,
            'test',
            1,
            false
            )
            ->shouldBeCalledOnce()
            ->willReturn($adjustment)
        ;
        $order->addAdjustment($adjustment)->shouldBeCalledOnce();

        $this->apply($order, $zone);
    }

    public function it_can_apply_taxes_when_shipment_method_tax_rate_can_be_resolved(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        SyliusTaxRateResolverInterface $shipmentMethodBasedTaxRateResolver,
        TaxRateResolverInterface $highestRatBasedTaxRateResolver,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $shipments,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $shippingTaxCategory,
        TaxRateInterface $taxRate,
        AdjustmentInterface $adjustment
    ): void {
        $this->beConstructedWith(
            $calculator,
            $adjustmentFactory,
            $shipmentMethodBasedTaxRateResolver,
            $highestRatBasedTaxRateResolver
        );

        $order->getShippingTotal()->shouldBeCalledOnce()->willReturn(1);
        $order->getShipments()->shouldBeCalledOnce()->willReturn($shipments);
        $shipments->first()->shouldBeCalledOnce()->willReturn($shipment);
        $shipment->getMethod()->shouldBeCalledOnce()->willReturn($shippingMethod);
        $shippingMethod->getTaxCategory()->shouldBeCalledOnce()->willReturn($shippingTaxCategory);
        $shipmentMethodBasedTaxRateResolver
            ->resolve($shippingMethod, ['zone' => $zone])
            ->shouldBeCalledOnce()
            ->willReturn($taxRate)
        ;
        $calculator->calculate(1, $taxRate)->shouldBeCalledOnce()->willReturn(1.0);
        $taxRate->getLabel()->shouldBeCalledOnce()->willReturn('test');
        $taxRate->isIncludedInPrice()->shouldBeCalledOnce()->willReturn(false);
        $adjustmentFactory
            ->createWithData(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'test',
                1,
                false
            )
            ->shouldBeCalledOnce()
            ->willReturn($adjustment)
        ;
        $order->addAdjustment($adjustment)->shouldBeCalledOnce();

        $this->apply($order, $zone);
    }
}
