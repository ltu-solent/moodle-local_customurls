@local @local_customurls
Feature: Testing editcustomurl in local_customurls
  As a site admin
  In order to provide pretty links I can edit customlinks
  I can add and edit customlinks

  Background:
    Given the following config values are set as admin:
    | checkurl | 0     | local_customurls |
    And I log in as "admin"
    And the following "local_customurls > customurls" exist:
    | custom_name | url                    | info                    |
    | users       | /admin/user.php        | browse users            |
    | dashboard   | /my                    | Dashboard               |

  Scenario: I can see customlinks
    Given I am logged in as admin
    When I visit "/local/customurls/index.php"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description  | Custom link | Redirect to     |
    | Dashboard    | dashboard   | /my             |
    | browse users | users       | /admin/user.php |

  Scenario: I can add a customlink
    Given I am logged in as admin
    When I visit "/local/customurls/index.php"
    And I follow "New CustomUrl"
    And I set the following fields to these values:
    | custom_name | me                   |
    | url         | /user/profile.php    |
    | info        | My user profile page |
    And I press "Save changes"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description          | Custom link | Redirect to       |
    | My user profile page | me          | /user/profile.php |

  Scenario: I try to add a link with an existing customlink
    Given I am logged in as admin
    And I visit "/local/customurls/index.php"
    And I follow "New CustomUrl"
    And I set the following fields to these values:
    | custom_name | dashboard       |
    | url         | /my/courses.php |
    | info        | My courses      |
    When I press "Save changes"
    Then I should see "Custom path already exists"
    And I set the field "custom_name" to "dashboard2"
    When I press "Save changes"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description | Custom link | Redirect to     |
    | My courses  | dashboard2  | /my/courses.php |
    When I click on "Edit" "link" in the "dashboard2" "table_row"
    And I set the field "custom_name" to "dashboard"
    When I press "Save changes"
    Then I should see "Custom path already exists"
    When I set the field "custom_name" to "dashboard2"
    And I press "Save changes"
    Then I should see "\"dashboard2\" has been updated."
