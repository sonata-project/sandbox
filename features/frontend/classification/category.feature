@frontend @category
Feature: Check the categories browsing and security

    @200
    Scenario: Check the correct display of a simple category
        Given I am on "shop/category"
        Then the response status code should be 200
        And I should see "Categories"

    Scenario: Browse categories to last level
        Given I am on "shop/category"
        And I follow "Goodies"
        Then I should not see "Products"
        And I should see "Subcategories"
        But I should see "Plushes"

        When I follow "Plushes"
        And I should see "Products"
        And I should see "PHP plush"

    Scenario: Check browsing non display of disabled category
        Given I am on "shop/category"
        And I follow "Goodies"
        Then I should not see "Shoes"

    Scenario: Check non direct access to disabled category
        Given I am on "shop/category/8/shoes"
        Then the response status code should be 404