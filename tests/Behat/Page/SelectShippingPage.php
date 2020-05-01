<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusHighestOrderShipmentTaxRatePlugin\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

final class SelectShippingPage extends SymfonyPage implements SelectShippingPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_select_shipping';
    }

    public function isTaxTotalEqualTo(string $taxTotal): bool
    {
        $element = $this->getDocument()->findById('sylius-summary-tax-excluded');
        Assert::notNull($element);

        return $element->getText() === $taxTotal;
    }
}
