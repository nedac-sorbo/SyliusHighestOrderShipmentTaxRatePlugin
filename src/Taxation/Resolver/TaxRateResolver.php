<?php

declare(strict_types=1);

namespace Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver;

use Psr\Log\LoggerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\TaxRateInterface;

final class TaxRateResolver implements TaxRateResolverInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Finds the highest tax rate in the order that matches the given zone.
     */
    public function resolve(OrderInterface $order, ZoneInterface $zone): ?TaxRateInterface
    {
        $items = $order->getItems();
        if (count($items) === 0) {
            $this->logger->debug(
                'TaxRateResolver was requested to resolve the highest tax rate in an order without items!'
            );
        }

        $zoneId = $zone->getId();

        /** @var null|TaxRateInterface $highest */
        $highest = null;
        foreach ($items as $item) {
            $variant = $item->getVariant();
            if (null !== $variant) {
                $taxCategory = $variant->getTaxCategory();
                if (null !== $taxCategory) {
                    $rates = $taxCategory->getRates();
                    if (count($rates) === 0) {
                        $this->logger->warning(
                            'TaxRateResolver found a tax category without rates!'
                        );
                    }

                    /** @var TaxRateInterface $rate */
                    foreach ($rates as $rate) {
                        $rateZone = $rate->getZone();
                        if (null !== $rateZone) {
                            if ($rateZone->getId() === $zoneId) {
                                if (null === $highest || $rate->getAmount() > $highest->getAmount()) {
                                    $highest = $rate;
                                }
                            }
                        } else {
                            $this->logger->debug('TaxRateResolver found tax rate without a zone!');
                        }
                    }
                } else {
                    $this->logger->debug('TaxRateResolver found a variant without a tax category!');
                }
            } else {
                $this->logger->debug(
                    'TaxRateResolver found an order item of which the variant does not have a tax category assigned!'
                );
            }
        }

        return $highest;
    }
}
