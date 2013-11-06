@backend
Feature: Check the media admin module

Scenario: Check media admin pages when not connected
  When I go to "admin/sonata/media/media/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check media admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  Then I should see "Filters"

Scenario: Add a new media with errors
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/create?provider=sonata.media.provider.youtube&context=default&uniqid=f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during item creation."

Scenario: Add a new youtube video
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/create?provider=sonata.media.provider.youtube&context=default&uniqid=f155592a220e"
  And I fill in "f155592a220e_binaryContent" with "6jlTfnfmbqM"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Add a new dailymotion video
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/create?provider=sonata.media.provider.dailymotion&context=default&uniqid=f155592a220e"
  And I fill in "f155592a220e_binaryContent" with "xnn4ge_l-oiseau-rebelle_shortfilms"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I follow "xls"
  Then the response status code should be 200

Scenario: Filter medias
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I fill in "filter_name_value" with "Best of Our Wokrs from June to December 2011"
  And I press "Filter"
  Then I should see "Best of Our Wokrs from June to December 2011"

Scenario: Edit a media
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I fill in "filter_name_value" with "Symfony 2 by Fabien Potencier"
  And I press "Filter"
  And I follow "Edit"
  And I press "Update"
  Then I should see "Item has been successfully updated."


Scenario: View a media
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I fill in "filter_name_value" with "Symfony 2 by Fabien Potencier"
  And I press "Filter"
  And I follow "Show"
  Then print last response
  Then I should see "Symfony 2 by Fabien Potencier"
  Then I should see "Preview ~ reference"

Scenario: View revisions of a media
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I follow "Edit"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: Delete a media
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I fill in "filter_name_value" with "Best of Our Wokrs from June to December 2011"
  And I press "Filter"
  And I follow "Edit"
  And I follow link "Delete" with class "btn btn-danger"
  And I press "Yes, delete"
  Then I should see "Item has been deleted successfully."
