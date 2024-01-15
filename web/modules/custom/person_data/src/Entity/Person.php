<?php declare(strict_types = 1);

namespace Drupal\person_data\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\person_data\PersonInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the person entity class.
 *
 * @ContentEntityType(
 *   id = "person_data_person",
 *   label = @Translation("person"),
 *   label_collection = @Translation("persons"),
 *   label_singular = @Translation("person"),
 *   label_plural = @Translation("persons"),
 *   label_count = @PluralTranslation(
 *     singular = "@count persons",
 *     plural = "@count persons",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\person_data\PersonListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\person_data\PersonAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\person_data\Form\PersonForm",
 *       "edit" = "Drupal\person_data\Form\PersonForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "person",
 *   admin_permission = "administer person_data_person",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "collection" = "/admin/content/person",
 *     "add-form" = "/admin/structure/person/add",
 *     "canonical" = "/admin/structure/person/{person_data_person}",
 *     "edit-form" = "/admin/structure/person/{person_data_person}/edit",
 *     "delete-form" = "/admin/structure/person/{person_data_person}/delete",
 *     "delete-multiple-form" = "/admin/content/person/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.person_data_person.settings",
 * )
 */
final class Person extends RevisionableContentEntityBase implements PersonInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }
  

  /**
   * {@inheritdoc}
   */
  /**
 * {@inheritdoc}
 */
public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

  // Adding the required fields - name, id, location, age
  $fields['name'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Name'))
    ->setDescription(t('The name of the Person entity.'))
    ->setSettings([
      'max_length' => 255,
      'text_processing' => 0,
    ])
    // Set no default value.
    ->setDefaultValue(NULL)
    ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -6,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -6,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);
  
  $fields['id'] = BaseFieldDefinition::create('integer')
    ->setLabel(t('ID'))
    ->setDescription(t('The ID of the Person entity.'))
    ->setRequired(TRUE)
    ->setSetting('max_length', 32);
  
  $fields['location'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Location'))
    ->setDescription(t('The location of the person entity.'))
    ->setSettings([
      'max_length' => 255,
      'text_processing' => 0,
    ])
    // Set no default value.
    ->setDefaultValue(NULL)
    ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -6,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -6,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);

  $fields['age'] = BaseFieldDefinition::create('integer')
    ->setLabel(t('Age'))
    ->setDescription(t('The age of the person.'));
  
  return $fields;
}


}
