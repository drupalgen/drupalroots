<?php

namespace Drupal\genealogy_notes;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface GenealogyNotesStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Notes revision IDs for a specific Notes.
   *
   * @param \Drupal\genealogy_notes\Entity\GenealogyNotesInterface $entity
   *   The Notes entity.
   *
   * @return int[]
   *   Notes revision IDs (in ascending order).
   */
  public function revisionIds(GenealogyNotesInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Notes author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Notes revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\genealogy_notes\Entity\GenealogyNotesInterface $entity
   *   The Notes entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(GenealogyNotesInterface $entity);

  /**
   * Unsets the language for all Notes with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
