<?php

namespace Drupal\example;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Example entity.
 *
 * @see \Drupal\example\Entity\example.
 */
class exampleAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\example\Entity\exampleInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished example entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published example entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit example entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete example entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add example entities');
  }

}
