<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the 'task_field_task' field type.
 *
 * @FieldType(
 *   id = "task_field_task",
 *   label = @Translation("Task"),
 *   description = @Translation("Field to support tasks with status, priority, deadline, timer, etc."),
 *   default_widget = "task_field_task_widget_default",
 *   default_formatter = "task_field_task_formatter_default",
 * )
 */
final class TaskItem extends FieldItemBase {
  use StringTranslationTrait;
  // /**
  //  * {@inheritdoc}
  //  */
  // public static function defaultStorageSettings(): array {
  //   $settings = ['fo_active' => ''];
  //   return $settings + parent::defaultStorageSettings();
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
  //   $element['fo_active'] = [
  //     '#type' => 'textfield',
  //     '#title' => $this->t('fo_active'),
  //     '#default_value' => $this->getSetting('fo_active'),
  //     '#disabled' => $has_data,
  //   ];
  //   return $element;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public static function defaultFieldSettings(): array {
  //   $settings = ['fo_active' => ''];
  //   return $settings + parent::defaultFieldSettings();
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
  //   $element['fo_active'] = [
  //     '#type' => 'textfield',
  //     '#title' => $this->t('Active'),
  //     '#default_value' => $this->getSetting('fo_active'),
  //   ];
  //   return $element;
  // }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return match ($this->get('status')->getValue()) {
      NULL, '' => TRUE,
      default => FALSE,
    };
  }


  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['deadline'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('Deadline'))
      ->setRequired(FALSE);

    $properties['archive'] = DataDefinition::create('boolean')
      ->setLabel(t('Archive'))
      ->setRequired(FALSE);

    $properties['timer'] = DataDefinition::create('boolean')
      ->setLabel(t('Timer'))
      ->setRequired(FALSE);

    $properties['status'] = DataDefinition::create('string')
      ->setLabel(t('Status'))
      ->setRequired(FALSE);

    $properties['priority'] = DataDefinition::create('string')
      ->setLabel(t('Priority'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    // $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();

    // // @DCG Suppose our value must not be longer than 10 characters.
    // $options['task']['Length']['max'] = 140;

    // // @DCG
    // // See /core/lib/Drupal/Core/Validation/Plugin/Validation/Constraint
    // // directory for available constraints.
    // $constraints[] = $constraint_manager->create('ComplexData', $options);
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    return [
      'columns' => [
        'deadline' => [
          'type' => 'date',
        ],
        'archive' => [
          'type' => 'boolean',
          'default' => 0,
        ],
        'timer' => [
          'type' => 'boolean',
        ],
        'status' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'priority' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();
    $values['status'] = $random->word(mt_rand(1, 50));
    return $values;
  }

}
