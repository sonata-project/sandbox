@backend
Feature: Check the post admin module

Scenario: Check comment admin pages when not connected
  When I go to "admin/sonata/news/post/list"
  Then the response status code should be 200
  And I should see "Username"

Scenario: Check post admin pages when connected
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  Then I should see "Filters"

Scenario: Add a new post with some errors
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/create?uniqid=f155592a220e"
  And I press "Create"
  Then I should see "An error has occurred during the creation of item \"n/a\"."

Scenario: Add a new post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/create?uniqid=f155592a220e"
  And I fill in "f155592a220e_title" with "toto"
  And I fill in "f155592a220e_abstract" with "abstract"
  And I select "markdown" from "f155592a220e_content_contentFormatter"
  And I fill in "f155592a220e_content_rawContent" with "raw content"
  And I select "2" from "f155592a220e_commentsDefaultStatus_2"
  And I press "Create"
  Then I should see "Item \"toto\" has been successfully created."

Scenario: Edit a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "toto"
  And I press "Update"
  Then I should see "Item \"toto\" has been successfully updated."

Scenario: View revision of a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "toto"
  And I follow "Revisions"
  Then the response status code should be 200

Scenario: View the last revision of a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "toto"
  And I follow "Show"
  Then the response status code should be 200

Scenario: Set ACL of a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "toto"
  And I follow "ACL"
  And I check "form_2VIEW"
  And I check "form_2EDIT"
  And I press "Update ACL"
  Then I should see "ACL has been successfuly updated."
  And the checkbox "form_1OWNER" should be checked
  And the checkbox "form_2VIEW" should be checked
  And the checkbox "form_2EDIT" should be checked
  And the checkbox "form_2DELETE" should not be checked
  And the checkbox "form_2MASTER" should not be checked
  And the checkbox "form_1VIEW" should not be checked
  And the checkbox "form_1EDIT" should not be checked
  And the checkbox "form_1UNDELETE" should not be checked
  And the checkbox "form_3EDIT" should not be checked

Scenario: Update ACL of a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "toto"
  And I follow "ACL"
  And I check "form_2DELETE"
  And I uncheck "form_2EDIT"
  And I press "Update ACL"
  Then I should see "ACL has been successfuly updated."
  And the checkbox "form_2DELETE" should be checked
  And the checkbox "form_2VIEW" should be checked
  And the checkbox "form_2EDIT" should not be checked
  And the checkbox "form_1EDIT" should not be checked
  And the checkbox "form_3MASTER" should not be checked

  Scenario: Filter posts
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I fill in "filter_title_value" with "toto"
  And I press "Filter"
  Then I should see "title"

Scenario: Export JSON data
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "json"
  Then the response status code should be 200

Scenario: Export CSV data
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "csv"
  Then the response status code should be 200

Scenario: Export XML data
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "xml"
  Then the response status code should be 200

Scenario: Export XLS data
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I follow "xls"
  Then the response status code should be 200

Scenario: Delete a post
  When I am connected with "admin" and "admin" on "admin/sonata/news/post/list"
  And I fill in "filter_title_value" with "toto"
  And I press "Filter"
  And I follow "toto"
  And I follow link "Delete" with class "btn btn-danger"
  And I press "Yes, delete"
  Then I should see "Item \"toto\" has been deleted successfully."
