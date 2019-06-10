<?php

namespace Drupal\events\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;


/**
 * Defines the Event entity entity.
 *
 * @ingroup events
 *
 * @ContentEntityType(
 *   id = "event_entity",
 *   label = @Translation("Event entity"),
 *   handlers = {
 *     "storage" = "Drupal\events\EventEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\events\EventEntityListBuilder",
 *     "views_data" = "Drupal\events\Entity\EventEntityViewsData",
 *     "translation" = "Drupal\events\EventEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\events\Form\EventEntityForm",
 *       "add" = "Drupal\events\Form\EventEntityForm",
 *       "edit" = "Drupal\events\Form\EventEntityForm",
 *       "delete" = "Drupal\events\Form\EventEntityDeleteForm",
 *     },
 *     "access" = "Drupal\events\EventEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\events\EventEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "event_entity",
 *   data_table = "event_entity_field_data",
 *   revision_table = "event_entity_revision",
 *   revision_data_table = "event_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer event entity entities",
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
 *     "canonical" = "/event_entity/{event_entity}",
 *     "add-form" = "/event_entity/add",
 *     "edit-form" = "/event_entity/{event_entity}/edit",
 *     "delete-form" = "/event_entity/{event_entity}/delete",
 *     "version-history" = "/event_entity/{event_entity}/revisions",
 *     "revision" = "/event_entity/{event_entity}/revisions/{event_entity_revision}/view",
 *     "revision_revert" = "/event_entity/{event_entity}/revisions/{event_entity_revision}/revert",
 *     "revision_delete" = "/event_entity/{event_entity}/revisions/{event_entity_revision}/delete",
 *     "translation_revert" = "/event_entity/{event_entity}/revisions/{event_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/event_entity",
 *   },
 *   field_ui_base_route = "event_entity.settings"
 * )
 */
class EventEntity extends RevisionableContentEntityBase implements EventEntityInterface {

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

    // If no revision author has been set explicitly, make the event_entity owner the
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
      ->setDescription(t('The user ID of author of the Event entity entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Event entity entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Event entity is published.'))
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
    //custom field
    $fields['description'] = BaseFieldDefinition::create('text_long')
     ->setLabel(t('Description'))
     ->setDescription(t('event description'))
     ->setRevisionable(TRUE)
     ->setTranslatable(TRUE)
     ->setDisplayOptions('form', array(
       'type' => 'string_textarea',
       'format'=>'plain_text',
       'settings' => array(
         'display_label' => TRUE,
       ),
     ))
    ->setDisplayOptions('view', array(
       'label' => 'hidden',
       'type' => 'string',
     ))
     ->setDisplayConfigurable('form', TRUE)
     ->setDisplayConfigurable('view', TRUE)
     ->setRequired(TRUE);

   $fields['lieu'] = BaseFieldDefinition::create('string')
     ->setLabel(t('Place'))
     ->setDescription(t('Place where the event will be presented'))
     ->setRevisionable(TRUE)
     ->setTranslatable(TRUE)
     ->setDisplayOptions('form', array(
       'type' => 'string_textfield',
       'settings' => array(
         'display_label' => TRUE,
       ),
     ))
    ->setDisplayOptions('view', array(
       'label' => 'hidden',
       'type' => 'string',
     ))
     ->setDisplayConfigurable('form', TRUE)
     ->setDisplayConfigurable('view', TRUE)
     ->setRequired(TRUE);


     $fields['manager'] = BaseFieldDefinition::create('entity_reference')
       ->setLabel(t('Event Manager'))
       ->setDescription(t('The user who organises the event.'))
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

     $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('Image field'))
      ->setSettings([
        'file_directory' => 'IMAGE_FOLDER',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ])
      ->setCardinality(5)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'default',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'label' => 'hidden',
        'type' => 'image_image',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start date'))
      ->setDescription(t('date when the event starts'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date'
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => 14,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 14,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['end_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End date'))
      ->setDescription(t('date when the event will finish'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date'
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => 14,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 14,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    

    return $fields;
  }

}
