@api
Feature: Test PathAuto Configuration
  In order to demonstrate that PathAuto is configured
  As a tester
  I need to verify the site configuration for PathAuto module

  Scenario:  Check PathAuto Settings
    Given I run drush "cget" "pathauto.settings"
    Then drush output should contain:
    """
    enabled_entity_types:
      - user
    punctuation:
      hyphen: 1
    verbose: false
    separator: '-'
    max_length: 100
    max_component_length: 100
    transliterate: true
    reduce_ascii: false
    case: true
    ignore_words: 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, since, than, the, this, that, to, up, via, with'
    update_action: 2
    """

  Scenario:  Check PathAuto Default Content Pattern
    Given I run drush "cget" "pathauto.pattern.default_content_pattern"
    Then drush output should contain:
    """
    id: default_content_pattern
    label: 'Default Content Pattern'
    type: 'canonical_entities:node'
    pattern: '[node:content-type]/[node:title]'
    selection_criteria: {  }
    selection_logic: and
    weight: 0
    relationships: {  }
    """
    And drush output should contain:
    """
    langcode: en
    status: true
    dependencies:
      module:
        - node
    """
  Scenario:  Check PathAuto Systems Actions for Node Aliases
    Given I run drush "cget" "system.action.pathauto_update_alias_node"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies:
      module:
        - pathauto
    """
    And drush output should contain:
    """
    id: pathauto_update_alias_node
    label: 'Update URL alias'
    type: node
    plugin: pathauto_update_alias
    configuration: {  }
    """

  Scenario:  Check PathAuto System Actions for User Aliases
    Given I run drush "cget" "system.action.pathauto_update_alias_user"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies:
      module:
        - pathauto
    """
    And drush output should contain:
    """
    id: pathauto_update_alias_user
    label: 'Update URL alias'
    type: user
    plugin: pathauto_update_alias
    configuration: {  }
    """

  Scenario:  Check PathAuto Default pattern for Taxonomy Terms
    Given I run drush "cget" "pathauto.pattern.default_taxonomy_term_pattern"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies:
      module:
        - taxonomy
    """
    And drush output should contain:
    """
    id: default_taxonomy_term_pattern
    label: 'Default Taxonomy Term Pattern'
    type: 'canonical_entities:taxonomy_term'
    pattern: '[term:vocabulary]/[term:name]'
    selection_criteria: {  }
    selection_logic: and
    weight: 0
    relationships: {  }
    """