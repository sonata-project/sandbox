@frontend @basket
Feature: Basket
    In order to buy some products
    As a visitor
    I want to be able to add products to the basket

    @200
    Scenario: Check default empty basket
        Given I am on "shop/category"
        When I go to "shop/basket"
        Then I should see "Go back shopping"
        And the response status code should be 200

    Scenario: Adding simple product to basket by browsing (product page)
        Given I am on "shop/basket"
        When I go to "shop/catalog"
        And I follow "Plushes"
        And I follow "PHP plush"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to basket"
        Then I should see "Your basket"
        And I should see "PHP plush"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "3"

    Scenario: Adding a variation product to basket by browsing (product page)
        Given I am on "shop/catalog"
        And I follow "Trainings"
        And I follow "Sonata trainings"
        And I follow "PHP working training"
        And I follow "PHP working training for beginners"
        And I press "Add to basket"
        Then I should see "Your basket"
        And I should see "PHP working training for beginners"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "1"

  Scenario: Changing product quantity from basket
        Given I am on "shop/catalog"
        And I follow "Plushes"
        And I follow "PHP plush"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to basket"
        And I should see "Your basket"
        And I should see "PHP plush"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "3"
        When I fill in "sonata_basket_basket_basketElements_0_quantity" with "12"
        And I press "Update the basket"
        Then I should see "Your basket"
        And I should see "PHP plush"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "12"

    Scenario: Remove a simple product from basket by browsing (product page)
        When I go to "shop/catalog"
        And I follow "Plushes"
        And I follow "PHP plush"
        And I fill in "add_basket_quantity" with "3"
        And I press "Add to basket"
        And I should see "Your basket"
        And I should see "PHP plush"
        And I check "sonata_basket_basket_basketElements_0_delete"
        And I press "Update the basket"
        Then I should see "Your basket is empty"