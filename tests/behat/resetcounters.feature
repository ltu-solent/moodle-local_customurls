@local @local_customurls
Feature: Testing resetcounters in local_customurls
  As a site admin
  In order to track new updates
  Periodically I want to reset counters

  Background:
    Given the following config values are set as admin:
    | checkurl | 0     | local_customurls |
    And I log in as "admin"
    And the following "local_customurls > customurls" exist:
    | custom_name | url             | info         | accesscount |
    | users       | /admin/user.php | browse users | 5           |
    | dashboard   | /my             | Dashboard    | 100         |
    | mycourse    | /my/courses.php | Courses      | 15          |

  Scenario: I reset a single counter
    Given I am logged in as admin
    When I visit "/local/customurls/index.php"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description  | Custom link | Redirect to     | Access count |
    | Dashboard    | dashboard   | /my             | 100          |
    | browse users | users       | /admin/user.php | 5            |
    | Courses      | mycourse    | /my/courses.php | 15           |
    When I click on "Reset access count" "link" in the "users" "table_row"
    Then I should see "Count reset for users"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description  | Custom link | Redirect to     | Access count |
    | Dashboard    | dashboard   | /my             | 100          |
    | browse users | users       | /admin/user.php | 0            |
    | Courses      | mycourse    | /my/courses.php | 15           |

  Scenario: I reset all counters
    Given I am logged in as admin
    When I visit "/local/customurls/index.php"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description  | Custom link | Redirect to     | Access count |
    | Dashboard    | dashboard   | /my             | 100          |
    | browse users | users       | /admin/user.php | 5            |
    | Courses      | mycourse    | /my/courses.php | 15           |
    When I click on "Reset access count" "link"
    Then I should see "Confirm reset counters"
    And I should see "All counters will be reset to zero. There is no recovery. Are you sure?"
    When I press "Confirm reset counters"
    Then the following should exist in the "local_customurls-customurls" table:
    | Description  | Custom link | Redirect to     | Access count |
    | Dashboard    | dashboard   | /my             | 0            |
    | browse users | users       | /admin/user.php | 0            |
    | Courses      | mycourse    | /my/courses.php | 0            |
    And I should see "All counters have been reset"
