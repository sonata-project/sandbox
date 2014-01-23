@frontend @order
Feature: Order
  In order to buy some products
  As a visitor
  I want to be able to retrieve correct prices in basket, order and invoice

  @200
  Scenario: Adding 3 PHP plushes to basket and verify final review, order and invoice pages prices
    Given I am connected with "johndoe" and "johndoe" on "shop/basket"
    When I go to "shop/catalog"
    And I follow "Plushes"
    And I follow "PHP plush"
    And I fill in "add_basket_quantity" with "3"
    And I press "Add to basket"
    Then I should see "Your basket"
    And I should see "PHP plush"
    And the "sonata_basket_basket_basketElements_0_quantity" field should contain "3"
    And I should see "29,99 €"
    And I should see "89,97 €"
    And I should see "69,93 €"
    And I should see "20,04 €"
    And I should see "89,97 €"
    Then I go to "shop/basket/step/delivery/address"
    And I press "Use selected"
    Then I should see "Free delivery"
    And I press "Update the delivery step"
    Then I press "Use selected"
    And I press "Update the payment step"
    Then I should see "Your basket"
    And I should see "PHP plush"
    And I should see "29,99 €"
    And I should see "89,97 €"
    And I should see "69,93 €"
    And I should see "20,04 €"
    And I should see "89,97 €"
    Then I check "basket_tac"
    And I press "Process to payment"
    Then I go to "shop/user/order"
    And I should see "89,97 €"
    Then I follow the last listed link of section "table table-bordered"
    And I should see "Your order"
    And I should see "PHP plush"
    And I should see "29,99 €"
    And I should see "89,97 €"
    And I should see "69,93 €"
    And I should see "20,04 €"
    And I should see "89,97 €"
    Then I follow the first link of section "col-sm-2 pull-right"
    And I should see "Your invoice"
    And I should see "PHP plush"
    And I should see "29,99 €"
    And I should see "89,97 €"
    And I should see "69,93 €"
    And I should see "20,04 €"
    And I should see "89,97 €"