<?php

namespace Drupal\example;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class exampleStorage extends SqlContentEntityStorage implements exampleStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(exampleInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {example_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {example_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(exampleInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {example_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('example_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
