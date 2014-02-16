@frontend @order
Feature: Order
  In order to buy some products
  As a visitor
  I want to be able to retrieve correct prices in basket, order and invoice

  @200
  Scenario Outline: Buy some products and ensure final review, order and invoice pages prices are correct
    Given I am connected with "johndoe" and "johndoe" on "shop/basket"
    When I go to "shop/catalog"
    And I follow "<category>"
    And I follow "<product>"
    And I fill in "add_basket_quantity" with "<quantity>"
    And I press "Add to basket"
    Then I should see "Your basket"
    And I should see "<product>"
    And the "sonata_basket_basket_basketElements_0_quantity" field should contain "<quantity>"
    And I should see "<price_vat>"
    And I should see "<price_total_vat>"
    And I should see "<total_no_vat>"
    And I should see "<vat>"
    And I should see "<total_vat>"
    Then I go to "shop/basket/step/delivery/address"
    Then I go to "shop/user/address/edit/30"
    Then I fill in "sonata_customer_address_countryCode" with "FR"
    Then I press "Save your address"
    And I should see "Your address has been successfully saved!"
    Then I press "Use selected"
    And I should see "Free delivery"
    Then I press "Update the delivery step"
    Then I press "Use selected"
    Then I press "Update the payment step"
    And I should see "Your basket"
    And I should see "<product>"
    And I should see "Delivery charge"
    And I should see "0,00"
    And I should see "<price_vat>"
    And I should see "<price_total_vat>"
    And I should see "<total_no_vat>"
    And I should see "<vat>"
    And I should see "<total_vat>"
    Then I check "basket_tac"
    And I press "Process to payment"
    And I should see "Confirmation payment valid"
    Then I follow the first link of class "btn btn-primary pull-right"
    And I should see "Your order"
    And I should see "<product>"
    And I should see "Delivery"
    And I should see "0,00"
    And I should see "<price_vat>"
    And I should see "<price_total_vat>"
    And I should see "<total_no_vat>"
    And I should see "<vat>"
    And I should see "<total_vat>"
    Then I follow the first link of class "btn btn-primary pull-right"
    And I should see "Your invoice"
    And I should see "<product>"
    And I should see "<price_vat>"
    And I should see "<price_total_vat>"
    And I should see "<total_no_vat>"
    And I should see "<vat>"
    And I should see "<total_vat>"

    Examples:
        | category | product        | quantity | price_vat | price_total_vat | vat    | total_no_vat | total_vat |
        | Plushes  | Blue PHP plush |     3    |  35,99    |     107,96      | 17,99  |    89,97     |   107,96  |
        | Mugs     | PHP mug        |     5    |  9,99     |     49,95       | 9,99   |    39,96     |   49,95   |
        | Mugs     | PHP mug        |     8    |  9,99     |     79,92       | 15,98  |    63,94     |   79,92   |