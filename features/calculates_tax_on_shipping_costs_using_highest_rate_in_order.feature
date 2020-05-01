@highest_order_shipment_tax_rate @javascript
Feature:
  As a customer
  I need the highest tax rate in the order to be applied to the shipping costs
  In order to be charged the right amount of tax on the shipping costs

  Background:
    Given the store operates on a single channel in "United States"
    And the store has tax categories "zero", "low" and "high"
    And the store has "Tax Free" tax rate of 0% for "zero" within the "US" zone
    And the store has "Low Tax" tax rate of 13% for "low" within the "US" zone
    And the store has "Regular Tax" tax rate of 21% for "high" within the "US" zone
    And the store has a product "Cat food"
    And this product's price is "$1"
    And the product "Cat food" belongs to "low" tax category
    And the store has a product "16k Flatscreen TV"
    And this product's price is "$10000"
    And the product "16k Flatscreen TV" belongs to "high" tax category
    And the store has a product "1L Vodka"
    And this product's price is "$12"
    And the product "1L Vodka" belongs to "zero" tax category
    And the store has "Flat rate" shipping method with "$10" fee

  Scenario: When no tax category is chosen for shipping method, the highest rate in the order is used
    When I have product "Cat food" in the cart
    And I have product "16k Flatscreen TV" in the cart
    And I have product "1L Vodka" in the cart
    And I am at the checkout addressing step
    And I specify the email as "customer@example.com"
    And I specify the shipping address as "city", "street", "postcode", "United States" for "Mister Customer"
    And I complete the addressing step
    Then I should see a total taxes of "$2,102.23"

  Scenario: When a tax category is chosen for shipping method, the rate from the assigned tax category is used
    And shipping method "Flat rate" belongs to "zero" tax category
    When I have product "Cat food" in the cart
    And I have product "16k Flatscreen TV" in the cart
    And I have product "1L Vodka" in the cart
    And I am at the checkout addressing step
    And I specify the email as "customer@example.com"
    And I specify the shipping address as "city", "street", "postcode", "United States" for "Mister Customer"
    And I complete the addressing step
    Then I should see a total taxes of "$2,100.13"
