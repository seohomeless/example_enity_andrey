<?php

namespace Drupal\example;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\example\Entity\exampleInterface;

/**
 * Defines the storage handler class for Example entities.
 *
 * This extends the base storage class, adding required special handling for
 * Example entities.
 *
 * @ingroup example
 */
interface exampleStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Example revision IDs for a specific Example.
   *
   * @param \Drupal\example\Entity\exampleInterface $entity
   *   The Example entity.
   *
   * @return int[]
   *   Example revision IDs (in ascending order).
   */
  public function revisionIds(exampleInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Example author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Example revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\example\Entity\exampleInterface $entity
   *   The Example entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(exampleInterface $entity);

  /**
   * Unsets the language for all Example with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
