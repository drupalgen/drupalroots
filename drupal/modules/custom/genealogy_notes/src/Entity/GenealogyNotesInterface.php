<?php

namespace Drupal\genealogy_notes\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Notes entities.
 *
 * @ingroup genealogy_notes
 */
interface GenealogyNotesInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Notes name.
   *
   * @return string
   *   Name of the Notes.
   */
  public function getName();

  /**
   * Sets the Notes name.
   *
   * @param string $name
   *   The Notes name.
   *
   * @return \Drupal\genealogy_notes\Entity\GenealogyNotesInterface
   *   The called Notes entity.
   */
  public function setName($name);

  /**
   * Gets the Notes creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Notes.
   */
  public function getCreatedTime();

  /**
   * Sets the Notes creation timestamp.
   *
   * @param int $timestamp
   *   The Notes creation timestamp.
   *
   * @return \Drupal\genealogy_notes\Entity\GenealogyNotesInterface
   *   The called Notes entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Notes published status indicator.
   *
   * Unpublished Notes are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Notes is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Notes.
   *
   * @param bool $published
   *   TRUE to set this Notes to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\genealogy_notes\Entity\GenealogyNotesInterface
   *   The called Notes entity.
   */
  public function setPublished($published);

  /**
   * Gets the Notes revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Notes revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\genealogy_notes\Entity\GenealogyNotesInterface
   *   The called Notes entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Notes revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Notes revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\genealogy_notes\Entity\GenealogyNotesInterface
   *   The called Notes entity.
   */
  public function setRevisionUserId($uid);

}
