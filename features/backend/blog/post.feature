@backend
Feature: Check the post admin module

  Scenario: Check comment admin pages when not connected
    When I go to "admin/app/news-post/list"
    Then the response status code should be 200
    And I should see "Authentication"

  Scenario: Check post admin pages when connected
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    Then I should see "Filters"

  Scenario: Add a new post with some errors
    When I am connected with "admin" and "admin" on "admin/app/news-post/create?uniqid=f155592a220e"
    And I press "Create"
    Then I should see "An error has occurred during the creation of item \"n/a\"."

  Scenario: Add a new post
    When I am connected with "admin" and "admin" on "admin/app/news-post/create?uniqid=f155592a220e"
    And I fill in "f155592a220e_title" with "toto"
    And I fill in "f155592a220e_abstract" with "abstract"
    And I select "markdown" from "f155592a220e_content_contentFormatter"
    And I fill in "f155592a220e_content_rawContent" with "raw content"
    And I select "2" from "f155592a220e_commentsDefaultStatus_2"
    And I press "Create"
    Then I should see "Item \"toto\" has been successfully created."

  @keep
  Scenario: Edit a post
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I fill in "filter_title_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I press "Update"
    Then I should see "Item \"toto\" has been successfully updated."

  @keep
  Scenario: View revision of a post
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I fill in "filter_title_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow "Revisions"
    Then the response status code should be 200

  @keep
  Scenario: View the last revision of a post
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I fill in "filter_title_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow "Show"
    Then the response status code should be 200

# Need to improve performance and ACL edition, which is not usable for large users dataset
#
#  @keep
#  Scenario: Set ACL of a post
#    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
#    And I fill in "filter_title_value" with "toto"
#    And I press "Filter"
#    And I follow "toto"
#    And I follow "ACL"
#    And I check "acl_roles_form_0_VIEW"
#    And I check "acl_roles_form_0_EDIT"
#    And I press "Update ACL"
#    Then I should see "ACL has been successfuly updated."
#    And the checkbox "acl_roles_form_0_VIEW" should be checked
#    And the checkbox "acl_roles_form_0_EDIT" should be checked
#    And the checkbox "acl_roles_form_0_DELETE" should not be checked
#    And the checkbox "acl_roles_form_0_UNDELETE" should not be checked
#    And the checkbox "acl_roles_form_0_OPERATOR" should not be checked
#    And the checkbox "acl_roles_form_0_MASTER" should not be checked
#    And the checkbox "acl_roles_form_0_OWNER" should not be checked
#
#  @keep
#  Scenario: Update ACL of a post
#    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
#    And I fill in "filter_title_value" with "toto"
#    And I press "Filter"
#    And I follow "toto"
#    And I follow "ACL"
#    And I check "acl_roles_form_0_DELETE"
#    And I uncheck "acl_roles_form_0_EDIT"
#    And I press "Update ACL"
#    Then I should see "ACL has been successfuly updated."
#    And the checkbox "acl_roles_form_0_VIEW" should be checked
#    And the checkbox "acl_roles_form_0_EDIT" should not be checked
#    And the checkbox "acl_roles_form_0_DELETE" should be checked
#    And the checkbox "acl_roles_form_0_UNDELETE" should not be checked
#    And the checkbox "acl_roles_form_0_OPERATOR" should not be checked
#    And the checkbox "acl_roles_form_0_MASTER" should not be checked
#    And the checkbox "acl_roles_form_0_OWNER" should not be checked

  @keep
  Scenario: Filter posts
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I fill in "filter_title_value" with "toto"
    And I press "Filter"
    Then I should see "title"

  @keep
  Scenario: Delete a post
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I fill in "filter_title_value" with "toto"
    And I press "Filter"
    And I follow "toto"
    And I follow link "Delete" with class "btn btn-danger"
    And I press "Yes, delete"
    Then I should see "Item \"toto\" has been deleted successfully."

  Scenario: Export JSON data
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I follow "JSON"
    Then the response status code should be 200

  Scenario: Export CSV data
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I follow "CSV"
    Then the response status code should be 200

  Scenario: Export XML data
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I follow "XML"
    Then the response status code should be 200

  Scenario: Export XLS data
    When I am connected with "admin" and "admin" on "admin/app/news-post/list"
    And I follow "XLS"
    Then the response status code should be 200

