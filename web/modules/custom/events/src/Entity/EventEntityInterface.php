<?php

namespace Drupal\events\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Event entity entities.
 *
 * @ingroup events
 */
interface EventEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Event entity name.
   *
   * @return string
   *   Name of the Event entity.
   */
  public function getName();

  /**
   * Sets the Event entity name.
   *
   * @param string $name
   *   The Event entity name.
   *
   * @return \Drupal\events\Entity\EventEntityInterface
   *   The called Event entity entity.
   */
  public function setName($name);

  /**
   * Gets the Event entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Event entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Event entity creation timestamp.
   *
   * @param int $timestamp
   *   The Event entity creation timestamp.
   *
   * @return \Drupal\events\Entity\EventEntityInterface
   *   The called Event entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Event entity published status indicator.
   *
   * Unpublished Event entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Event entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Event entity.
   *
   * @param bool $published
   *   TRUE to set this Event entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\events\Entity\EventEntityInterface
   *   The called Event entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Event entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Event entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\events\Entity\EventEntityInterface
   *   The called Event entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Event entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Event entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\events\Entity\EventEntityInterface
   *   The called Event entity entity.
   */
  public function setRevisionUserId($uid);

}
