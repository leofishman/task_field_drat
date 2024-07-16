<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'task_field_progress' field type.
 *
 * @FieldType(
 *   id = "task_field_progress",
 *   label = @Translation("Progress"),
 *   description = @Translation("Stores task progress information including priority, status, timer, and deadline."),
 *   default_widget = "task_field_progress",
 *   default_formatter = "task_field_progress_default",
 * )
 */
final class ProgressItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    $settings = [
      'priority_options' => static::getDefaultPriorityOptions(),
      'status_options' => static::getDefaultStatusOptions(),
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    $settings = $this->getSettings();

    $element['priority_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Priority options'),
      '#default_value' => $this->optionsToString($settings['priority_options']),
      '#description' => $this->t('Enter one value per line, in the format key|label.'),
      '#disabled' => $has_data,
    ];

    $element['status_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Status options'),
      '#default_value' => $this->optionsToString($settings['status_options']),
      '#description' => $this->t('Enter one value per line, in the format key|label.'),
      '#disabled' => $has_data,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    return [
      'default_priority' => 10,
      'default_status' => 'new',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $settings = $this->getSettings();
    $element['default_priority'] = [
      '#type' => 'select',
      '#title' => $this->t('Default Priority'),
      '#options' => $this->getPriorityOptions(),
      '#default_value' => $settings['default_priority'],
    ];

    $element['default_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Default Status'),
      '#options' => $this->getStatusOptions(),
      '#default_value' => $settings['default_status'],
    ];

    return $element;
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
    // @todo Add more constraints here if necessary.
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
      // @todo Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();

    $values['priority'] = array_rand(static::getDefaultPriorityOptions());
    $values['timer'] = (bool) mt_rand(0, 1);
    $values['status'] = array_rand(static::getDefaultStatusOptions());

    $timestamp = \Drupal::time()->getRequestTime() - mt_rand(0, 86400 * 365);
    $values['deadline'] = gmdate('Y-m-d\TH:i:s', $timestamp);

    return $values;
  }

  /**
   * Convert an array of options to a string for form display.
   */
  protected function optionsToString(array $options): string {
    $lines = [];
    foreach ($options as $key => $label) {
      $lines[] = "$key|$label";
    }
    return implode("\n", $lines);
  }

}
