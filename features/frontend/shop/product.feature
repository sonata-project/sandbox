@frontend @product
Feature: Products
    In order to select some products
    As a visitor
    I want to be able to browse products

    Background:
        Given I am on "shop/basket"

    @200
    Scenario: Check products & categories page status code
        When I go to "shop/category"
        Then I should see "Categories"
        And the response status code should be 200

    Scenario: Browse products by categories
        When I go to "shop/category"
        And I follow "Goodies"
        And I follow "Plushes"
        Then I should see "PHP plush"
        But I should not see "No subcategories available"

    Scenario: Select a product by browsing products
        When I go to "shop/category"
        And I follow "Goodies"
        And I follow "Plushes"
        And I follow "PHP plush"
        Then I should see "Vincent Pontier"

    @200
    Scenario: Browse out of stock product
        When I go to "shop/product/3/php-t-shirt"
        Then the response status code should be 200
        And I should see "Warning : this product is currently out of stock !"

    @404
    Scenario: Browse disabled product
        When I go to "shop/product/4/air-max-sonata-limited-edition"
        Then the response status code should be 404

    @200
    Scenario: Direct access to a product without a picture
        When I go to "shop/product/5/air-max-sonata-ultimate-edition"
        Then the response status code should be 200
        And I should see "Get this ULTIMATE edition"
