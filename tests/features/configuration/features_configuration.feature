@api
Feature: Test Features Setup
  In order to demonstrate that Features is configured
  As a tester
  I need to verify the site configuration for Features

  Scenario:  Check Features Settings
    Given I run drush "cget" "features.settings"
    Then drush output should contain:
    """
    export:
      folder: custom/features
    langcode: en
    """
  Scenario:  Check Features Bundle Configuration
    Given I run drush "cget" "features.bundle.configuration"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: Configuration
    machine_name: configuration
    description: ''
    assignments:
      alter:
        core: true
        uuid: true
        user_permissions: true
        enabled: true
        weight: 0
      base:
        types:
          config:
            comment_type: comment_type
            node_type: node_type
          content:
            user: user
        enabled: true
        weight: -2
      core:
        types:
          config:
            date_format: date_format
            field_storage_config: field_storage_config
            entity_form_mode: entity_form_mode
            image_style: image_style
            menu: menu
            responsive_image_style: responsive_image_style
            user_role: user_role
            entity_view_mode: entity_view_mode
        enabled: true
        weight: 5
      dependency:
        enabled: true
        weight: 15
      exclude:
        types:
          config:
            features_bundle: features_bundle
        curated: true
        module:
          installed: true
          profile: true
          namespace: true
          namespace_any: false
        enabled: true
        weight: -5
      existing:
        enabled: true
        weight: 12
      forward_dependency:
        enabled: true
        weight: 4
      namespace:
        enabled: true
        weight: 0
      optional:
        types:
          config: {  }
        enabled: true
        weight: 0
      packages:
        enabled: true
        weight: -20
      profile:
        curated: true
        standard:
          files: true
          dependencies: true
        types:
          config:
            block: block
            language_content_settings: language_content_settings
            configurable_language: configurable_language
            migration: migration
            shortcut_set: shortcut_set
            tour: tour
        enabled: true
        weight: 10
      site:
        types:
          config:
            action: action
            contact_form: contact_form
            block_content_type: block_content_type
            rdf_mapping: rdf_mapping
            search_page: search_page
            taxonomy_vocabulary: taxonomy_vocabulary
            editor: editor
            filter_format: filter_format
        enabled: true
        weight: 7
    profile_name: ''
    is_profile: false
    """

  Scenario:  Check Features Bundle Content Types configuration
    Given I run drush "cget" "features.bundle.content_types"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: 'Content Types'
    machine_name: content_types
    description: ''
    assignments:
      alter:
        core: true
        uuid: true
        user_permissions: true
        enabled: true
        weight: 0
      base:
        types:
          config:
            comment_type: comment_type
            node_type: node_type
          content:
            user: user
        enabled: true
        weight: -2
      core:
        types:
          config:
            date_format: date_format
            field_storage_config: field_storage_config
            entity_form_mode: entity_form_mode
            image_style: image_style
            menu: menu
            responsive_image_style: responsive_image_style
            user_role: user_role
            entity_view_mode: entity_view_mode
        enabled: true
        weight: 5
      dependency:
        enabled: true
        weight: 15
      exclude:
        types:
          config:
            features_bundle: features_bundle
        curated: true
        module:
          installed: true
          profile: true
          namespace: true
          namespace_any: false
        enabled: true
        weight: -5
      existing:
        enabled: true
        weight: 12
      forward_dependency:
        enabled: true
        weight: 4
      namespace:
        enabled: true
        weight: 0
      optional:
        types:
          config: {  }
        enabled: true
        weight: 0
      packages:
        enabled: true
        weight: -20
      profile:
        curated: true
        standard:
          files: true
          dependencies: true
        types:
          config:
            block: block
            language_content_settings: language_content_settings
            configurable_language: configurable_language
            migration: migration
            shortcut_set: shortcut_set
            tour: tour
        enabled: true
        weight: 10
      site:
        types:
          config:
            action: action
            contact_form: contact_form
            block_content_type: block_content_type
            rdf_mapping: rdf_mapping
            search_page: search_page
            taxonomy_vocabulary: taxonomy_vocabulary
            editor: editor
            filter_format: filter_format
        enabled: true
        weight: 7
    profile_name: ''
    is_profile: false
    """

  Scenario:  Check Features Bundle Views configuration
    Given I run drush "cget" "features.bundle.views"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: Views
    machine_name: views
    description: ''
    assignments:
      alter:
        core: true
        uuid: true
        user_permissions: true
        enabled: true
        weight: 0
      base:
        types:
          config:
            comment_type: comment_type
            node_type: node_type
          content:
            user: user
        enabled: true
        weight: -2
      core:
        types:
          config:
            date_format: date_format
            field_storage_config: field_storage_config
            entity_form_mode: entity_form_mode
            image_style: image_style
            menu: menu
            responsive_image_style: responsive_image_style
            user_role: user_role
            entity_view_mode: entity_view_mode
        enabled: true
        weight: 5
      dependency:
        enabled: true
        weight: 15
      exclude:
        types:
          config:
            features_bundle: features_bundle
        curated: true
        module:
          installed: true
          profile: true
          namespace: true
          namespace_any: false
        enabled: true
        weight: -5
      existing:
        enabled: true
        weight: 12
      forward_dependency:
        enabled: true
        weight: 4
      namespace:
        enabled: true
        weight: 0
      optional:
        types:
          config: {  }
        enabled: true
        weight: 0
      packages:
        enabled: true
        weight: -20
      profile:
        curated: true
        standard:
          files: true
          dependencies: true
        types:
          config:
            block: block
            language_content_settings: language_content_settings
            configurable_language: configurable_language
            migration: migration
            shortcut_set: shortcut_set
            tour: tour
        enabled: true
        weight: 10
      site:
        types:
          config:
            action: action
            contact_form: contact_form
            block_content_type: block_content_type
            rdf_mapping: rdf_mapping
            search_page: search_page
            taxonomy_vocabulary: taxonomy_vocabulary
            editor: editor
            filter_format: filter_format
        enabled: true
        weight: 7
    profile_name: ''
    is_profile: false
    """

  Scenario:  Check Features Bundle Default settings
    Given I run drush "cget" "features.bundle.default"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: Default
    machine_name: default
    description: ''
    assignments:
      alter:
        core: true
        uuid: true
        user_permissions: true
        enabled: true
        weight: 0
      base:
        types:
          config:
            comment_type: comment_type
            node_type: node_type
          content:
            user: user
        enabled: true
        weight: -2
      core:
        types:
          config:
            date_format: date_format
            field_storage_config: field_storage_config
            entity_form_mode: entity_form_mode
            image_style: image_style
            menu: menu
            responsive_image_style: responsive_image_style
            user_role: user_role
            entity_view_mode: entity_view_mode
        enabled: true
        weight: 5
      dependency:
        enabled: true
        weight: 15
      exclude:
        types:
          config:
            features_bundle: features_bundle
        curated: true
        module:
          installed: true
          profile: true
          namespace: true
          namespace_any: false
        enabled: true
        weight: -5
      existing:
        enabled: true
        weight: 12
      forward_dependency:
        enabled: true
        weight: 4
      namespace:
        enabled: true
        weight: 0
      optional:
        types:
          config: {  }
        enabled: true
        weight: 0
      packages:
        enabled: true
        weight: -20
      profile:
        curated: true
        standard:
          files: true
          dependencies: true
        types:
          config:
            block: block
            language_content_settings: language_content_settings
            configurable_language: configurable_language
            migration: migration
            shortcut_set: shortcut_set
            tour: tour
        enabled: true
        weight: 10
      site:
        types:
          config:
            action: action
            contact_form: contact_form
            block_content_type: block_content_type
            rdf_mapping: rdf_mapping
            search_page: search_page
            taxonomy_vocabulary: taxonomy_vocabulary
            editor: editor
            filter_format: filter_format
        enabled: true
        weight: 7
    profile_name: ''
    is_profile: false
    """

  Scenario:  Check Features Bundle Taxonomy settings
    Given I run drush "cget" "features.bundle.taxonomy"
    Then drush output should contain:
    """
    langcode: en
    status: true
    dependencies: {  }
    """
    And drush output should contain:
    """
    name: Taxonomy
    machine_name: taxonomy
    """
    And drush output should contain:
    """
    assignments:
      alter:
        core: true
        uuid: true
        user_permissions: true
        enabled: true
        weight: 0
      base:
        types:
          config:
            comment_type: comment_type
            node_type: node_type
          content:
            user: user
        enabled: true
        weight: -2
      core:
        types:
          config:
            date_format: date_format
            field_storage_config: field_storage_config
            entity_form_mode: entity_form_mode
            image_style: image_style
            menu: menu
            responsive_image_style: responsive_image_style
            user_role: user_role
            entity_view_mode: entity_view_mode
        enabled: true
        weight: 5
      dependency:
        enabled: true
        weight: 15
      exclude:
        types:
          config:
            features_bundle: features_bundle
        curated: true
        module:
          installed: true
          profile: true
          namespace: true
          namespace_any: false
        enabled: true
        weight: -5
      existing:
        enabled: true
        weight: 12
      forward_dependency:
        enabled: true
        weight: 4
      namespace:
        enabled: true
        weight: 0
      optional:
        types:
          config: {  }
        enabled: true
        weight: 0
      packages:
        enabled: true
        weight: -20
      profile:
        curated: true
        standard:
          files: true
          dependencies: true
        types:
          config:
            block: block
            language_content_settings: language_content_settings
            configurable_language: configurable_language
            migration: migration
            shortcut_set: shortcut_set
            tour: tour
        enabled: true
        weight: 10
      site:
        types:
          config:
            action: action
            contact_form: contact_form
            block_content_type: block_content_type
            rdf_mapping: rdf_mapping
            search_page: search_page
            taxonomy_vocabulary: taxonomy_vocabulary
            editor: editor
            filter_format: filter_format
        enabled: true
        weight: 7
    profile_name: ''
    is_profile: false
    """
