<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Behat\Page\SelectShippingPageInterface;
use Webmozart\Assert\Assert;

final class Shipping implements Context
{
    private SelectShippingPageInterface $page;

    public function __construct(SelectShippingPageInterface $page)
    {
        $this->page = $page;
    }

    /**
     * @Then I should see a total taxes of :taxes
     */
    public function iShouldSeeATotalTaxesOf(string $taxes): void
    {
        Assert::true($this->page->isTaxTotalEqualTo($taxes));
    }
}
