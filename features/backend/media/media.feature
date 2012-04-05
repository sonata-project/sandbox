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
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I go to "admin/sonata/media/media/create?provider=sonata.media.provider.youtube&context=default&uniqid=4f155592a220e"
  And I fill in "4f155592a220e_binaryContent" with "6jlTfnfmbqMdzdzd"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Add a new youtube video
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I go to "admin/sonata/media/media/create?provider=sonata.media.provider.youtube&context=default&uniqid=4f155592a220e"
  And I fill in "4f155592a220e_binaryContent" with "6jlTfnfmbqM"
  And I press "Create"
  Then I should see "Item has been successfully created."

Scenario: Add a new dailymotion video
  When I am connected with "admin" and "admin" on "admin/sonata/media/media/list"
  And I go to "admin/sonata/media/media/create?provider=sonata.media.provider.dailymotion&context=default&uniqid=4f155592a220e"
  And I fill in "4f155592a220e_binaryContent" with "xnn4ge_l-oiseau-rebelle_shortfilms"
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
