@frontaend @cart
Feature: Cart
    In order to buy some products
    As a visitor
    I want to be able to add products to cart

    @200
    Scenario: Check default empty cart
        Given I am on "shop/category"
        When I go to "shop/basket"
        Then I should see "Your cart is empty"
        And the response status code should be 200

    Scenario: Adding simple product to cart by browsing (product page)
        Given I am on "shop/basket"
        When I go to "shop/category"
        And I follow "Mac"
        And I follow "MacBook Air"
        And I follow "Product 163"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to cart"
        Then I should see "Your cart"
        And I should see "Product 163"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "3"

    Scenario: Changing product quantity from cart
        Given I am on "shop/category"
        And I follow "Mac"
        And I follow "MacBook Air"
        And I follow "Product 163"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to cart"
        And I should see "Your cart"
        And I should see "Product 163"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "3"
        When I fill in "sonata_basket_basket_basketElements_0_quantity" with "12"
        And I press "Update the cart"
        Then I should see "Your cart"
        And I should see "Product 163"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "12"

    Scenario: Remove a simple product from cart by browsing (product page)
        When I go to "shop/category"
        And I follow "Mac"
        And I follow "MacBook Air"
        And I follow "Product 163"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to cart"
        And I should see "Your cart"
        And I should see "Product 163"
        And I check "sonata_basket_basket_basketElements_0_delete"
        And I press "Update the cart"
        Then I should see "Your cart is empty"