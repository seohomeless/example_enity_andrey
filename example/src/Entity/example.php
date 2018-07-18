<?php

namespace Drupal\example\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Example entity.
 *
 * @ingroup example
 *
 * @ContentEntityType(
 *   id = "example",
 *   label = @Translation("Example"),
 *   handlers = {
 *     "storage" = "Drupal\example\exampleStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\example\exampleListBuilder",
 *     "views_data" = "Drupal\example\Entity\exampleViewsData",
 *     "translation" = "Drupal\example\exampleTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\example\Form\exampleForm",
 *       "add" = "Drupal\example\Form\exampleForm",
 *       "edit" = "Drupal\example\Form\exampleForm",
 *       "delete" = "Drupal\example\Form\exampleDeleteForm",
 *     },
 *     "access" = "Drupal\example\exampleAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\example\exampleHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "example",
 *   data_table = "example_field_data",
 *   revision_table = "example_revision",
 *   revision_data_table = "example_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer example entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/example/{example}",
 *     "add-form" = "/admin/structure/example/add",
 *     "edit-form" = "/admin/structure/example/{example}/edit",
 *     "delete-form" = "/admin/structure/example/{example}/delete",
 *     "version-history" = "/admin/structure/example/{example}/revisions",
 *     "revision" = "/admin/structure/example/{example}/revisions/{example_revision}/view",
 *     "revision_revert" = "/admin/structure/example/{example}/revisions/{example_revision}/revert",
 *     "revision_delete" = "/admin/structure/example/{example}/revisions/{example_revision}/delete",
 *     "translation_revert" = "/admin/structure/example/{example}/revisions/{example_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/example",
 *   },
 *   field_ui_base_route = "example.settings"
 * )
 */
class example extends RevisionableContentEntityBase implements exampleInterface {

  use EntityChangedTrait;

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
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the example owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Example entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

  
		  
		$fields['title'] = BaseFieldDefinition::create('string')
		->setLabel(t('Title'))
		->setRequired(TRUE)
		->setTranslatable(TRUE)
		->setRevisionable(TRUE)
		->setSetting('max_length', 255)
		->setDisplayOptions('view', array(
		'label' => 'hidden',
		'type' => 'string',
		'weight' => -5,
	  ))
		->setDisplayOptions('form', array(
		'type' => 'string_textfield',
		'weight' => -5,
	  ))
		->setDisplayConfigurable('form', TRUE);

	  
	  	$fields['description'] = BaseFieldDefinition::create('string')
		->setLabel(t('Description'))
		->setTranslatable(TRUE)
		->setRevisionable(TRUE)
		->setSetting('max_length', 255)
		->setDisplayOptions('view', array(
		'label' => 'hidden',
		'type' => 'string',
		'weight' => -5,
	  ))
		->setDisplayOptions('form', array(
		'type' => 'string_textfield',
		'weight' => -5,
	  ))
		->setDisplayConfigurable('form', TRUE);

	  	$fields['url'] = BaseFieldDefinition::create('string')
		->setLabel(t('URL'))
		->setTranslatable(TRUE)
		->setRevisionable(TRUE)
		->setSetting('max_length', 255)
		->setDisplayOptions('view', array(
		'label' => 'hidden',
		'type' => 'string',
		'weight' => -5,
	  ))
		->setDisplayOptions('form', array(
		'type' => 'string_textfield',
		'weight' => -5,
	  ))
		->setDisplayConfigurable('form', TRUE);


    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Example is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
