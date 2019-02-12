<?php

namespace Drupal\events;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\events\Entity\EventEntityInterface;

/**
 * Defines the storage handler class for Event entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event entity entities.
 *
 * @ingroup events
 */
interface EventEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Event entity revision IDs for a specific Event entity.
   *
   * @param \Drupal\events\Entity\EventEntityInterface $entity
   *   The Event entity entity.
   *
   * @return int[]
   *   Event entity revision IDs (in ascending order).
   */
  public function revisionIds(EventEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Event entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Event entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\events\Entity\EventEntityInterface $entity
   *   The Event entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EventEntityInterface $entity);

  /**
   * Unsets the language for all Event entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
