<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'task_field_progress' field type.
 *
 * @FieldType(
 *   id = "task_field_progress",
 *   label = @Translation("progress"),
 *   description = @Translation("Stores task progress information including priority, status, timer, and deadline."),
 *   default_widget = "task_field_progress",
 *   default_formatter = "task_field_progress_default",
 * )
 */
final class ProgressItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return $this->priority === NULL && $this->timer != 1 && $this->status === NULL && $this->deadline === NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    $properties['priority'] = DataDefinition::create('integer')
      ->setLabel(t('Priority'));
    $properties['timer'] = DataDefinition::create('boolean')
      ->setLabel(t('Timer'));
    $properties['status'] = DataDefinition::create('string')
      ->setLabel(t('Status'));
    $properties['deadline'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Deadline'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    // @todo Add more constraints here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'priority' => [
        'type' => 'int',
        'size' => 'normal',
      ],
      'timer' => [
        'type' => 'int',
        'size' => 'tiny',
      ],
      'status' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'deadline' => [
        'type' => 'varchar',
        'length' => 20,
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {

    $random = new Random();

    $values['priority'] = mt_rand(-1000, 1000);

    $values['timer'] = (bool) mt_rand(0, 1);

    $values['status'] = $random->word(mt_rand(1, 255));

    $timestamp = \Drupal::time()->getRequestTime() - mt_rand(0, 86400 * 365);
    $values['deadline'] = gmdate('Y-m-d', $timestamp);

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function getDefaultPriorityOptions() {
    return [
      25 => t('Imminent'),
      20 => t('Urgent'),
      15 => t('Important'),
      10 => t('Normal'),
      5 => t('Low'),
      0 => t('None'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function getDefaultStatusOptions() {
    return [
      'new' => t('New'),
      'doing' => t('Doing'),
      'paused' => t('Paused'),
      'postponed' => t('Postponed'),
      'cancelled' => t('Cancelled'),
      'completed' => t('Completed'),
    ];
  }

  /**
   * Get the priority options.
   */
  public function getPriorityOptions() {
    $settings = $this->getSettings();
    return $settings['priority_options'] ?? static::getDefaultPriorityOptions();
  }

  /**
   * Get the status options.
   */
  public function getStatusOptions() {
    $settings = $this->getSettings();
    return $settings['status_options'] ?? static::getDefaultStatusOptions();
  }

}
