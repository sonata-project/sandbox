@frontend @category
Feature: Check the categories browsing and security

    @200
    Scenario: Check the correct display of catalog
        Given I am on "shop/catalog"
        Then the response status code should be 200
        And I should see "Catalog"
        And I should see "Goodies"
        And I should see "Travels"
        And I should not see "No products available"

    Scenario: Browse catalog to last level
        Given I am on "shop/catalog"
        And I follow "Plushes"
        Then I should not see "No products available"
        And I should see "PHP Plush"
        And I should see "Goodies"
        But I should see "Mugs"

        When I follow "Plushes"
        And I should see "Products"
        And I should see "PHP plush"

#    Scenario: Check browsing non display of empty category
#        Given I am on "shop/category"
#        And I follow "Symfony2"
#        Then I should see "No products available"

    Scenario: Check non direct access to disabled category
        Given I am on "shop/category/8/shoes"
        Then the response status code should be 404
