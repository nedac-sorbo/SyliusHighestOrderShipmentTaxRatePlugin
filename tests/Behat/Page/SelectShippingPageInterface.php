<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface SelectShippingPageInterface extends SymfonyPageInterface
{
    public function isTaxTotalEqualTo(string $taxTotal): bool;
}
