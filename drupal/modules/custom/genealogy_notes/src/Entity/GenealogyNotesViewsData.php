<?php

namespace Drupal\genealogy_notes\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Notes entities.
 */
class GenealogyNotesViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
