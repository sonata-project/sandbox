@frontend @products
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
        And I follow "Mac"
        And I follow "MacBook Air"
        Then I should see "Product 163"
        And I should see "No subcategories available"

    Scenario: Select a product by browsing products
        When I go to "shop/category"
        And I follow "Mac"
        And I follow "MacBook Air"
        And I follow "Product 163"
        Then I should see "Product description goes here"
        And I should see "Product gallery"