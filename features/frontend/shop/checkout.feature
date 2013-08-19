@frontend @checkout
Feature: Cart
    In order to buy products
    As a visitor
    I want to be able to use checkout process

    @200
    Scenario: No checkout for empty cart
        Given I am on "shop/category"
        When I go to "shop/basket"
        Then I should see "Your cart is empty"
        But I should not see "Next step"

    Scenario: Checkout with one product in the cart
        Given I am on "shop/basket"
        And I should see "Your cart is empty"
        And I go to "shop/category"
        And I follow "Mac"
        And I follow "MacBook Air"
        And I follow "Product 163"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to cart"
        And I follow "Next step"
        And I fill in "sonata_basket_address_name" with "Delivery name"
        And I fill in "sonata_basket_address_firstname" with "firstname"
        And I fill in "sonata_basket_address_lastname" with "lastname"
        And I fill in "sonata_basket_address_address1" with "Address 1"
        And I fill in "sonata_basket_address_address2" with "Address 2"
        And I fill in "sonata_basket_address_address3" with "Address 3"
        And I fill in "sonata_basket_address_postcode" with "ABC123"
        And I fill in "sonata_basket_address_city" with "New York"
        And I fill in "sonata_basket_address_countryCode" with "US"
        And I fill in "sonata_basket_address_phone" with "55512345"
        And I press "Update the delivery step"
        And I press "Update the delivery step"
        And I press "Update the payment step"        
        When I check "basket_tac"
        And I press "Process to payment"
        Then the response should contain "Confirmation payment valid"
        And the response should contain "Your payment is confirmed."