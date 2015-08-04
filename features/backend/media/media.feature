@backend
Feature: Check the media admin module

  Scenario: Check media admin pages when not connected
    When I go to "admin/app/media-media/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check media admin pages when connected
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    Then I should see "Filters"

  Scenario: Add a new media with errors
    When I am connected with "admin" and "admin" on "admin/app/media-media/create?provider=sonata.media.provider.youtube&context=default&uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"n/a\"."

  Scenario: Add a new youtube video
    When I am connected with "admin" and "admin" on "admin/app/media-media/create?provider=sonata.media.provider.youtube&context=default&uniqid=f155592a220e"
    And I fill in "f155592a220e_binaryContent" with "6jlTfnfmbqM"
    And I press "Create"
    Then I should see "Item \"Best of Works - FullSIX Group - June to December 2011\" has been successfully created."

  @keep
  Scenario: Filter medias
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I fill in "filter_name_value" with "Best of Works - FullSIX Group - June to December 2011"
    And I press "Filter"
    # truncated wording
    Then I should see "Best of Works"

  @keep
  Scenario: Delete youtube video
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I follow "Best of Works - FullSIX Group - June to..."
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"Best of Works - FullSIX Group - June to December 2011\" has been deleted successfully."

  Scenario: Add a new dailymotion video
    When I am connected with "admin" and "admin" on "admin/app/media-media/create?provider=sonata.media.provider.dailymotion&context=default&uniqid=f155592a220e"
    And I fill in "f155592a220e_binaryContent" with "xnn4ge_l-oiseau-rebelle_shortfilms"
    And I press "Create"
    Then I should see "Item \"L'Oiseau Rebelle\" has been successfully created."

  @keep
  Scenario: Delete dailymotion video
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I follow "L'Oiseau Rebelle"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"L'Oiseau Rebelle\" has been deleted successfully."

  Scenario: Edit a media
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I fill in "filter_context_value" with "product_catalog"
    And I press "Filter"
    And I follow link "Travels" with class "sonata-tree__item__edit"
    And I follow "Switzerland Travel"
    And I press "Update"
    Then I should see "Item \"Switzerland Travel\" has been successfully updated."

  Scenario: View a media
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I fill in "filter_context_value" with "product_catalog"
    And I press "Filter"
    And I follow link "Travels" with class "sonata-tree__item__edit"
    And I follow "Quebec Travel"
    Then I should see "Quebec Travel"
    Then I should see "Preview"

  Scenario: View revisions of a media
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I fill in "filter_context_value" with "product_catalog"
    And I press "Filter"
    And I follow link "Travels" with class "sonata-tree__item__edit"
    And I follow "Quebec Travel"
    And I follow "Revisions"
    Then the response status code should be 200

  Scenario: Add a Media, then delete it immediately
    When I am connected with "admin" and "admin" on "admin/app/media-media/create?provider=sonata.media.provider.image&context=default&uniqid=f155592a220e"
    And I attach the file "project/features/fixtures/sonata.jpg" to "f155592a220e_binaryContent"
    And I press "Create"
    Then I should see "has been successfully created."
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "has been deleted successfully."

  Scenario: Try to delete an undeletable Media
    When I am connected with "admin" and "admin" on "admin/app/media-media/list"
    And I fill in "filter_context_value" with "product_catalog"
    And I press "Filter"
    And I follow link "Paris" with class "sonata-tree__item__edit"
    And I follow "Paris 1"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "An Error has occurred during deletion of item \"Paris 1\"."

  Scenario: Export JSON data
    When I am connected with "admin" and "admin" on "admin/app/media-media/list?filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=php&filter%5BproviderReference%5D%5Btype%5D=&filter%5BproviderReference%5D%5Bvalue%5D=&filter%5Benabled%5D%5Btype%5D=&filter%5Benabled%5D%5Bvalue%5D=&filter%5Bcontext%5D%5Btype%5D=&filter%5Bcontext%5D%5Bvalue%5D=product_catalog&filter%5Bcategory%5D%5Btype%5D=&filter%5Bcategory%5D%5Bvalue%5D=14&filter%5Bwidth%5D%5Btype%5D=&filter%5Bwidth%5D%5Bvalue%5D=&filter%5Bheight%5D%5Btype%5D=&filter%5Bheight%5D%5Bvalue%5D=&filter%5BcontentType%5D%5Btype%5D=&filter%5BcontentType%5D%5Bvalue%5D=&filter%5BproviderName%5D%5Btype%5D=&filter%5BproviderName%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=id&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32&context=&category=14&hide_context="
    And I follow "JSON"
    Then the response status code should be 200

  Scenario: Export CSV data
    When I am connected with "admin" and "admin" on "admin/app/media-media/list?filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=php&filter%5BproviderReference%5D%5Btype%5D=&filter%5BproviderReference%5D%5Bvalue%5D=&filter%5Benabled%5D%5Btype%5D=&filter%5Benabled%5D%5Bvalue%5D=&filter%5Bcontext%5D%5Btype%5D=&filter%5Bcontext%5D%5Bvalue%5D=product_catalog&filter%5Bcategory%5D%5Btype%5D=&filter%5Bcategory%5D%5Bvalue%5D=14&filter%5Bwidth%5D%5Btype%5D=&filter%5Bwidth%5D%5Bvalue%5D=&filter%5Bheight%5D%5Btype%5D=&filter%5Bheight%5D%5Bvalue%5D=&filter%5BcontentType%5D%5Btype%5D=&filter%5BcontentType%5D%5Bvalue%5D=&filter%5BproviderName%5D%5Btype%5D=&filter%5BproviderName%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=id&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32&context=&category=14&hide_context="
    And I follow "CSV"
    Then the response status code should be 200

  Scenario: Export XML data
    When I am connected with "admin" and "admin" on "admin/app/media-media/list?filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=php&filter%5BproviderReference%5D%5Btype%5D=&filter%5BproviderReference%5D%5Bvalue%5D=&filter%5Benabled%5D%5Btype%5D=&filter%5Benabled%5D%5Bvalue%5D=&filter%5Bcontext%5D%5Btype%5D=&filter%5Bcontext%5D%5Bvalue%5D=product_catalog&filter%5Bcategory%5D%5Btype%5D=&filter%5Bcategory%5D%5Bvalue%5D=14&filter%5Bwidth%5D%5Btype%5D=&filter%5Bwidth%5D%5Bvalue%5D=&filter%5Bheight%5D%5Btype%5D=&filter%5Bheight%5D%5Bvalue%5D=&filter%5BcontentType%5D%5Btype%5D=&filter%5BcontentType%5D%5Bvalue%5D=&filter%5BproviderName%5D%5Btype%5D=&filter%5BproviderName%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=id&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32&context=&category=14&hide_context="
    And I follow "XML"
    Then the response status code should be 200

  Scenario: Export XLS data
    When I am connected with "admin" and "admin" on "admin/app/media-media/list?filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=php&filter%5BproviderReference%5D%5Btype%5D=&filter%5BproviderReference%5D%5Bvalue%5D=&filter%5Benabled%5D%5Btype%5D=&filter%5Benabled%5D%5Bvalue%5D=&filter%5Bcontext%5D%5Btype%5D=&filter%5Bcontext%5D%5Bvalue%5D=product_catalog&filter%5Bcategory%5D%5Btype%5D=&filter%5Bcategory%5D%5Bvalue%5D=14&filter%5Bwidth%5D%5Btype%5D=&filter%5Bwidth%5D%5Bvalue%5D=&filter%5Bheight%5D%5Btype%5D=&filter%5Bheight%5D%5Bvalue%5D=&filter%5BcontentType%5D%5Btype%5D=&filter%5BcontentType%5D%5Bvalue%5D=&filter%5BproviderName%5D%5Btype%5D=&filter%5BproviderName%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=id&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32&context=&category=14&hide_context="
    And I follow "XLS"
    Then the response status code should be 200
