<?php

namespace Drupal\genealogy_notes;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\genealogy_notes\Entity\GenealogyNotesInterface;

/**
 * Defines the storage handler class for Notes entities.
 *
 * This extends the base storage class, adding required special handling for
 * Notes entities.
 *
 * @ingroup genealogy_notes
 */
class GenealogyNotesStorage extends SqlContentEntityStorage implements GenealogyNotesStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(GenealogyNotesInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {genealogy_notes_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {genealogy_notes_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(GenealogyNotesInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {genealogy_notes_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('genealogy_notes_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
