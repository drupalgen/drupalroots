<?php

namespace Drupal\genealogy_notes;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Notes entities.
 *
 * @ingroup genealogy_notes
 */
class GenealogyNotesListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Notes ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\genealogy_notes\Entity\GenealogyNotes */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.genealogy_notes.edit_form',
      ['genealogy_notes' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
