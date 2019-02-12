<?php

namespace Drupal\events;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Event entity entity.
 *
 * @see \Drupal\events\Entity\EventEntity.
 */
class EventEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\events\Entity\EventEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished event entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published event entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit event entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete event entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add event entity entities');
  }

}
