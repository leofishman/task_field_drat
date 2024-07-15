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
 * Defines the 'task_field_parameters' field type.
 *
 * @FieldType(
 *   id = "task_field_parameters",
 *   label = @Translation("parameters"),
 *   description = @Translation("Some description."),
 *   default_widget = "task_field_parameters",
 *   default_formatter = "task_field_parameters_default",
 * )
 */
final class ParametersItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    $settings = ['foo' => 'example'];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    $settings = $this->getSettings();

    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $settings['foo'],
      '#disabled' => $has_data,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    $settings = ['bar' => 'example'];
    return $settings + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $settings = $this->getSettings();

    $element['bar'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bar'),
      '#default_value' => $settings['bar'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return $this->deadline === NULL && $this->status === NULL  && $this->status == NULL && $this->timer != 1;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    $properties['deadline'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Deadline'));
    $properties['status'] = DataDefinition::create('string')
      ->setLabel(t('status'));
    $properties['timer'] = DataDefinition::create('boolean')
      ->setLabel(t('Timer'));
    // $properties['priority'] = DataDefinition::create('integer')
    // ->setLabel(t('Priority'));

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
      'deadline' => [
        'type' => 'varchar',
        'length' => 20,
      ],
      'status' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'timer' => [
        'type' => 'int',
        'size' => 'tiny',
      ],
      // 'priority' => [
      //   'type' => 'int',
      //   'size' => 'tiny',
      //   'unsigned' => true,
      // ],
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
  public static function getPriorityOptions() {
    return [
      0 => t('Imminent'),
      1 => t('Urgent'),
      2 => t('Important'),
      3 => t('Normal'),
      4 => t('Low'),
      5 => t('None'),

    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {

    $random = new Random();

    $timestamp = \Drupal::time()->getRequestTime() - mt_rand(0, 86400 * 365);
    $values['deadline'] = gmdate('Y-m-d', $timestamp);

    $values['status'] = $random->word(mt_rand(1, 255));

    $values['timer'] = (bool) mt_rand(0, 1);

    // $values['priority'] = (int) mt_rand(0, 4);

    return $values;
  }

}
