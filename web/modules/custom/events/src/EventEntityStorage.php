<?php

namespace Drupal\events;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class EventEntityStorage extends SqlContentEntityStorage implements EventEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EventEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {event_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {event_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EventEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {event_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('event_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
