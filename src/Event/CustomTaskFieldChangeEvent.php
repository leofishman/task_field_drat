<?php

namespace Drupal\task_field\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Class CustomTaskFieldChangeEvent.
 */
class CustomTaskFieldChangeEvent extends Event {

  const EVENT_NAME = 'custom_task_field_change';

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * The field name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The original value.
   *
   * @var mixed
   */
  protected $originalValue;

  /**
   * The current value.
   *
   * @var mixed
   */
  protected $currentValue;

  /**
   * Constructs a new CustomTaskFieldChangeEvent.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name.
   * @param mixed $original_value
   *   The original value.
   * @param mixed $current_value
   *   The current value.
   */
  public function __construct(ContentEntityInterface $entity, $field_name, $original_value, $current_value) {
    $this->entity = $entity;
    $this->fieldName = $field_name;
    $this->originalValue = $original_value;
    $this->currentValue = $current_value;
  }

  /**
   * Gets the entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The entity.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Gets the field name.
   *
   * @return string
   *   The field name.
   */
  public function getFieldName() {
    return $this->fieldName;
  }

  /**
   * Gets the original value.
   *
   * @return mixed
   *   The original value.
   */
  public function getOriginalValue() {
    return $this->originalValue;
  }

  /**
   * Gets the current value.
   *
   * @return mixed
   *   The current value.
   */
  public function getCurrentValue() {
    return $this->currentValue;
  }
}
