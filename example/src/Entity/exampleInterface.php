<?php

namespace Drupal\example\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Example entities.
 *
 * @ingroup example
 */
interface exampleInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Example name.
   *
   * @return string
   *   Name of the Example.
   */
  public function getName();

  /**
   * Sets the Example name.
   *
   * @param string $name
   *   The Example name.
   *
   * @return \Drupal\example\Entity\exampleInterface
   *   The called Example entity.
   */
  public function setName($name);

  /**
   * Gets the Example creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Example.
   */
  public function getCreatedTime();

  /**
   * Sets the Example creation timestamp.
   *
   * @param int $timestamp
   *   The Example creation timestamp.
   *
   * @return \Drupal\example\Entity\exampleInterface
   *   The called Example entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Example published status indicator.
   *
   * Unpublished Example are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Example is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Example.
   *
   * @param bool $published
   *   TRUE to set this Example to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\example\Entity\exampleInterface
   *   The called Example entity.
   */
  public function setPublished($published);

  /**
   * Gets the Example revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Example revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\example\Entity\exampleInterface
   *   The called Example entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Example revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Example revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\example\Entity\exampleInterface
   *   The called Example entity.
   */
  public function setRevisionUserId($uid);

}
