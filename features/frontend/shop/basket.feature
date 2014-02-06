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

    Scenario Outline: Adding simple product to basket by browsing (product page)
        Given I am on "shop/basket"
        When I go to "shop/catalog"
        And I follow "Plushes"
        And I follow "PHP plush"
        And I fill in "add_basket_quantity" with "<quantity>"
        And I press "Add to basket"
        Then I should see "Your basket"
        And I should see "PHP plush"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "<value>"

        Examples:
            | quantity | value |
            |    1     |   1   |
            |    2     |   2   |
            |    3     |   3   |

    Scenario: Adding a variation product to basket by browsing (product page)
        Given I am on "shop/catalog"
        And I follow "Travels"
        And I follow "Paris"
        And I follow "Paris tour"
        And I press "Add to basket"
        Then I should see "Your basket"
        And I should see "Paris tour for small group"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "1"

  Scenario Outline: Changing product quantity from basket
        Given I am on "shop/catalog"
        And I follow "<category>"
        And I follow "<product>"
        And I fill in "add_basket_quantity" with "<quantity>"
        And I press "Add to basket"
        And I should see "Your basket"
        And I should see "<product>"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "<quantity>"
        When I fill in "sonata_basket_basket_basketElements_0_quantity" with "<new_quantity>"
        And I press "Update the basket"
        Then I should see "Your basket"
        And I should see "<product>"
        And the "sonata_basket_basket_basketElements_0_quantity" field should contain "<new_quantity>"

        Examples:
            | category | product       | quantity | new_quantity |
            | Plushes  | PHP plush     |    1     |      1       |
            | Plushes  | PHP plush     |    2     |      3       |
            | Mugs     | PHP mug       |    3     |      4       |

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
