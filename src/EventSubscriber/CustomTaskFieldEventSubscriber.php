<?php

namespace Drupal\task_field\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedEvent;

/**
 * Class CustomTaskFieldEventSubscriber.
 */
class CustomTaskFieldEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs a new CustomTaskFieldEventSubscriber.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EntityChangedEvent::class][] = ['onEntityChange', 20];
    return $events;
  }

  /**
   * Reacts to entity change events.
   *
   * @param \Drupal\Core\Entity\EntityChangedEvent $event
   *   The entity change event.
   */
  public function onEntityChange(EntityChangedEvent $event) {
    $entity = $event->getEntity();
    if ($entity instanceof ContentEntityInterface && $entity->hasField('task_field_task')) {
      $field_name = 'task_field_task';
      $field_definition = $entity->getFieldDefinition($field_name);
      if ($field_definition->getType() === 'task_field_task') {
        $original_entity = $entity->original ?? NULL;
        if ($original_entity) {
          $this->emitFieldChangeEvents($entity, $original_entity, $field_name);
        }
      }
    }
  }

  /**
   * Emits field change events.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The current entity.
   * @param \Drupal\Core\Entity\ContentEntityInterface $original_entity
   *   The original entity.
   * @param string $field_name
   *   The field name.
   */
  protected function emitFieldChangeEvents(ContentEntityInterface $entity, ContentEntityInterface $original_entity, $field_name) {
    $fields = [
      'name',
      'deadline',
      'archive',
      'timer',
      'status',
      'priority',
      'comments',
    ];

    foreach ($fields as $field) {
      $current_value = $entity->get($field_name)->{$field};
      $original_value = $original_entity->get($field_name)->{$field};

      if ($current_value !== $original_value) {
        $event = new CustomTaskFieldChangeEvent($entity, $field, $original_value, $current_value);
        \Drupal::service('event_dispatcher')->dispatch(CustomTaskFieldChangeEvent::EVENT_NAME, $event);
      }
    }
  }
}
