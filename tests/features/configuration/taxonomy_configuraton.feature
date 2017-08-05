@api
Feature: Test Taxonomy Configuration
  In order to demonstrate that Taxonomy is configured
  As a tester
  I need to verify the site configuration for Taxonomy

  Scenario:  Test Editorial Group Configuration
    Given I run drush "cget" "taxonomy.vocabulary.editorial_groups"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: 'Editorial Groups'
    vid: editorial_groups
    description: 'Editorial Groups for Content'
    hierarchy: 0
    weight: 0
    """

  Scenario:  Check for existence of "Corporate Communication" in the "Editorial Groups" vocabulary
    Given I am logged in as a user with the "administrator" role
    When I visit "/admin/structure/taxonomy/manage/editorial_groups/overview"
    Then I should see the link "Corporate Communications" in the "admin content" region
