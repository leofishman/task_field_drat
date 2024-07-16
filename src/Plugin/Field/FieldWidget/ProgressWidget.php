<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'task_field_progress' field widget.
 *
 * @FieldWidget(
 *   id = "task_field_progress",
 *   label = @Translation("Progress"),
 *   field_types = {"task_field_progress"},
 * )
 */
final class ProgressWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return ['foo' => 'bar'] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    // Fetch the field definition to get the configured options.
    $field_definition = $items->getFieldDefinition();
    $settings = $field_definition->getSettings();

    // Get the priority and status options.
    $priority_options = $settings['priority_options'];
    $status_options = $settings['status_options'];

    $element['priority'] = [
      '#type' => 'select',
      '#title' => $this->t('Priority'),
      '#options' => $items[$delta]->getDefaultPriorityOptions(),  // Use the method from ProgressItem
      '#default_value' => $items[$delta]->priority ?? NULL,
    ];

    $element['timer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Timer'),
      '#default_value' => $items[$delta]->timer ?? NULL,
    ];

    $element['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => $items[$delta]->getDefaultStatusOptions(),
      '#default_value' => $items[$delta]->status ?? NULL,
    ];

    $element['deadline'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Deadline'),
      '#default_value' => NULL,
      '#date_time_element' => 'none',
      '#date_time_format' => '',
    ];
    if (isset($items[$delta]->deadline)) {
      $element['deadline']['#default_value'] = DrupalDateTime::createFromFormat(
        'Y-m-d',
        $items[$delta]->deadline,
        'UTC'
      );
    }

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'task-field-progress-elements';
//    $element['#attached']['library'][] = 'task_field/task_field_progress';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $error, array $form, FormStateInterface $form_state): array|bool {
    $element = parent::errorElement($element, $error, $form, $form_state);
    if ($element === FALSE) {
      return FALSE;
    }
    $error_property = explode('.', $error->getPropertyPath())[1];
    return $element[$error_property];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => $value) {
      if ($value['priority'] === '') {
        $values[$delta]['priority'] = NULL;
      }
      if ($value['timer'] === '') {
        $values[$delta]['timer'] = NULL;
      }
      if ($value['status'] === '') {
        $values[$delta]['status'] = NULL;
      }
      if ($value['deadline'] === '') {
        $values[$delta]['deadline'] = NULL;
      }
      if ($value['deadline'] instanceof DrupalDateTime) {
        $values[$delta]['deadline'] = $value['deadline']->format('Y-m-d');
      }
    }
    return $values;
  }

}
