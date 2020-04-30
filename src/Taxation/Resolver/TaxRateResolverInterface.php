<?php

declare(strict_types=1);

namespace Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Taxation\Resolver;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\TaxRateInterface;

interface TaxRateResolverInterface
{
    public function resolve(OrderInterface $order, ZoneInterface $zone): ?TaxRateInterface;
}
