<?php declare(strict_types = 1);

namespace Drupal\person_data;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the person entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class PersonAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    return match($operation) {
      'view' => AccessResult::allowedIfHasPermissions($account, ['view person_data_person', 'administer person_data_person'], 'OR'),
      'update' => AccessResult::allowedIfHasPermissions($account, ['edit person_data_person', 'administer person_data_person'], 'OR'),
      'delete' => AccessResult::allowedIfHasPermissions($account, ['delete person_data_person', 'administer person_data_person'], 'OR'),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create person_data_person', 'administer person_data_person'], 'OR');
  }

}
