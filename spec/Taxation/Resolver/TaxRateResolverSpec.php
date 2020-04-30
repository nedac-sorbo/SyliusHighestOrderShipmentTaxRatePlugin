<?php

namespace spec\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver;

use Doctrine\Common\Collections\Collection;
use Iterator;
use Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver\TaxRateResolverInterface;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

class TaxRateResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(LoggerInterface $logger): void
    {
        $this->beConstructedWith($logger);
        $this->shouldHaveType(TaxRateResolverInterface::class);
    }

    /**
     * Checks that it can resolve the highest tax rate in an order with only one item that has a variant with
     * a tax category that has only one rate which matches the given zone.
     */
    public function it_can_resolve_the_highest_tax_rate_in_an_order_with_one_item_one_tax_category_one_rate(
        LoggerInterface $logger,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $items,
        Iterator $itemIterator,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        TaxCategoryInterface $taxCategory,
        Collection $rates,
        Iterator $rateIterator,
        TaxRateInterface $highTaxRate,
        ZoneInterface $rateZone
    ): void {
        $this->beConstructedWith($logger);

        $order->getItems()->shouldBeCalledOnce()->willReturn($items);
        $items->getIterator()->shouldBeCalledOnce()->willReturn($itemIterator);
        $items->count()->shouldBeCalledOnce()->willReturn(1);
        $itemIterator->rewind()->shouldBeCalledOnce();
        $itemIterator->next()->shouldBeCalledOnce();
        $itemIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $itemIterator->current()->shouldBeCalledOnce()->willReturn($item);
        $item->getVariant()->shouldBeCalledOnce()->willReturn($variant);
        $variant->getTaxCategory()->shouldBeCalledOnce()->willReturn($taxCategory);
        $taxCategory->getRates()->shouldBeCalledOnce()->willReturn($rates);
        $rates->count()->shouldBeCalledOnce()->willReturn(1);
        $rates->getIterator()->shouldBeCalledOnce()->willReturn($rateIterator);
        $rateIterator->rewind()->shouldBeCalledOnce();
        $rateIterator->next()->shouldBeCalledOnce();
        $rateIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $rateIterator->current()->shouldBeCalledOnce()->willReturn($highTaxRate);
        $highTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $rateZone->getId()->shouldBeCalledOnce()->willReturn(1);
        $zone->getId()->shouldBeCalledOnce()->willReturn(1);

        $this
            ->resolve($order, $zone)
            ->shouldBeAnInstanceOf(TaxRateInterface::class)
        ;
    }

    /**
     * Check that the highest tax rate can be resolved in orders with only one item that has a variant with a
     * tax category that has 3 tax rates that match the given zone.
     */
    public function it_can_resolve_rate_in_orders_with_one_item_with_three_tax_rates_matching_zone_in_one_category(
        LoggerInterface $logger,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $items,
        Iterator $itemIterator,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        TaxCategoryInterface $taxCategory,
        Collection $rates,
        Iterator $rateIterator,
        TaxRateInterface $highTaxRate,
        TaxRateInterface $lowTaxRate,
        TaxRateInterface $zeroTaxRate,
        ZoneInterface $rateZone
    ): void {
        $this->beConstructedWith($logger);

        $order->getItems()->shouldBeCalledOnce()->willReturn($items);
        $items->getIterator()->shouldBeCalledOnce()->willReturn($itemIterator);
        $items->count()->shouldBeCalledOnce()->willReturn(1);
        $itemIterator->rewind()->shouldBeCalledOnce();
        $itemIterator->next()->shouldBeCalledOnce();
        $itemIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $itemIterator->current()->shouldBeCalledOnce()->willReturn($item);
        $item->getVariant()->shouldBeCalledOnce()->willReturn($variant);
        $variant->getTaxCategory()->shouldBeCalledOnce()->willReturn($taxCategory);
        $taxCategory->getRates()->shouldBeCalledOnce()->willReturn($rates);
        $rates->count()->shouldBeCalledOnce()->willReturn(1);
        $rates->getIterator()->shouldBeCalledOnce()->willReturn($rateIterator);
        $rateIterator->rewind()->shouldBeCalledOnce();
        $rateIterator->next()->shouldBeCalledTimes(3);
        $rateIterator->valid()->shouldBeCalledTimes(4)->willReturn(true, true, true, false);
        $rateIterator->current()->shouldBeCalledTimes(3)->willReturn($zeroTaxRate, $lowTaxRate, $highTaxRate);
        $zeroTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $zeroTaxRate->getAmount()->shouldBeCalledOnce()->willReturn(0.0);
        $lowTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $lowTaxRate->getAmount()->shouldBeCalledTimes(2)->willReturn(0.13);
        $highTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $highTaxRate->getAmount()->shouldBeCalledOnce()->willReturn(0.21);
        $rateZone->getId()->shouldBeCalledTimes(3)->willReturn(1);
        $zone->getId()->shouldBeCalledOnce()->willReturn(1);

        $this
            ->resolve($order, $zone)
            ->shouldBeEqualTo($highTaxRate);
        ;
    }

    /**
     * Checks that the highest tax rate can be resolved in orders with 3 items that have variants with
     * 3 different tax categories, that all have only one tax rate and that tax rate matches the given zone.
     */
    public function it_can_resolve_rate_in_orders_with_multiple_items_multiple_categories_with_one_rate_zone_matches(
        LoggerInterface $logger,
        OrderInterface $order,
        ZoneInterface $zone,
        Collection $items,
        Iterator $itemIterator,
        OrderItemInterface $itemWithHighRate,
        ProductVariantInterface $variantWithHighRateCategory,
        TaxCategoryInterface $taxCategoryWithHighRate,
        Collection $highRates,
        Iterator $highRateIterator,
        OrderItemInterface $itemWithLowRate,
        ProductVariantInterface $variantWithLowRateCategory,
        TaxCategoryInterface $taxCategoryWithLowRate,
        Collection $lowRates,
        Iterator $lowRateIterator,
        OrderItemInterface $itemWithZeroRate,
        ProductVariantInterface $variantWithZeroRateCategory,
        TaxCategoryInterface $taxCategoryWithZeroRate,
        Collection $zeroRates,
        Iterator $zeroRateIterator,
        TaxRateInterface $highTaxRate,
        TaxRateInterface $lowTaxRate,
        TaxRateInterface $zeroTaxRate,
        ZoneInterface $rateZone
    ): void {
        $this->beConstructedWith($logger);

        $order->getItems()->shouldBeCalledOnce()->willReturn($items);
        $items->getIterator()->shouldBeCalledOnce()->willReturn($itemIterator);
        $items->count()->shouldBeCalledOnce()->willReturn(1);
        $itemIterator->rewind()->shouldBeCalledOnce();
        $itemIterator->next()->shouldBeCalledTimes(3);
        $itemIterator->valid()->shouldBeCalledTimes(4)->willReturn(true, true, true, false);
        $itemIterator->current()->shouldBeCalledTimes(3)->willReturn(
            $itemWithLowRate,
            $itemWithHighRate,
            $itemWithZeroRate
        );

        $itemWithHighRate->getVariant()->shouldBeCalledOnce()->willReturn($variantWithHighRateCategory);
        $variantWithHighRateCategory->getTaxCategory()->shouldBeCalledOnce()->willReturn($taxCategoryWithHighRate);
        $taxCategoryWithHighRate->getRates()->shouldBeCalledOnce()->willReturn($highRates);
        $highRates->count()->shouldBeCalledOnce()->willReturn(1);
        $highRates->getIterator()->shouldBeCalledOnce()->willReturn($highRateIterator);
        $highRateIterator->rewind()->shouldBeCalledOnce();
        $highRateIterator->next()->shouldBeCalledOnce();
        $highRateIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $highRateIterator->current()->shouldBeCalledOnce()->willReturn($highTaxRate);
        $highTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $highTaxRate->getAmount()->shouldBeCalled()->willReturn(0.21);

        $itemWithLowRate->getVariant()->shouldBeCalledOnce()->willReturn($variantWithLowRateCategory);
        $variantWithLowRateCategory->getTaxCategory()->shouldBeCalledOnce()->willReturn($taxCategoryWithLowRate);
        $taxCategoryWithLowRate->getRates()->shouldBeCalledOnce()->willReturn($lowRates);
        $lowRates->count()->shouldBeCalledOnce()->willReturn(1);
        $lowRates->getIterator()->shouldBeCalledOnce()->willReturn($lowRateIterator);
        $lowRateIterator->rewind()->shouldBeCalledOnce();
        $lowRateIterator->next()->shouldBeCalledOnce();
        $lowRateIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $lowRateIterator->current()->shouldBeCalledOnce()->willReturn($lowTaxRate);
        $lowTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $lowTaxRate->getAmount()->shouldBeCalled()->willReturn(0.13);

        $itemWithZeroRate->getVariant()->shouldBeCalledOnce()->willReturn($variantWithZeroRateCategory);
        $variantWithZeroRateCategory->getTaxCategory()->shouldBeCalledOnce()->willReturn($taxCategoryWithZeroRate);
        $taxCategoryWithZeroRate->getRates()->shouldBeCalledOnce()->willReturn($zeroRates);
        $zeroRates->count()->shouldBeCalledOnce()->willReturn(1);
        $zeroRates->getIterator()->shouldBeCalledOnce()->willReturn($zeroRateIterator);
        $zeroRateIterator->rewind()->shouldBeCalledOnce();
        $zeroRateIterator->next()->shouldBeCalledOnce();
        $zeroRateIterator->valid()->shouldBeCalledTimes(2)->willReturn(true, false);
        $zeroRateIterator->current()->shouldBeCalledOnce()->willReturn($zeroTaxRate);
        $zeroTaxRate->getZone()->shouldBeCalledOnce()->willReturn($rateZone);
        $zeroTaxRate->getAmount()->shouldBeCalled()->willReturn(0.0);

        $rateZone->getId()->shouldBeCalledTimes(3)->willReturn(1);
        $zone->getId()->shouldBeCalledOnce()->willReturn(1);

        $this
            ->resolve($order, $zone)
            ->shouldBeEqualTo($highTaxRate)
        ;
    }
}
