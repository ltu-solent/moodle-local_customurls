@local @local_customurls @sol
Feature: Manage customurls
    As the site administrator
    In order to allow url redirects
    I need to be able to add custom urls to the customurls database

    Background:
        Given I log in as "admin"
        And I navigate to "Plugins > Custom Urls > Custom Urls" in the site administration

    Scenario: Add a new customurl
        Given I press "New CustomUrl"
        And I set the following fields to these values:
            | Original Url | 
