<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'task_field_parameters' field widget.
 *
 * @FieldWidget(
 *   id = "task_field_parameters",
 *   label = @Translation("parameters"),
 *   field_types = {"task_field_parameters"},
 * )
 */
final class ParametersWidget extends WidgetBase {

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

    $element['status'] = [
      '#type' => 'textfield',
      '#title' => $this->t('status'),
      '#default_value' => $items[$delta]->status ?? NULL,
    ];

    $element['timer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Timer'),
      '#default_value' => $items[$delta]->timer ?? NULL,
    ];

    // $priority_value = isset($items[$delta]->priority) ? $items[$delta]->priority : '';
    // $element['priority'] = [
    //   '#type' => 'select',
    //   '#title' => $this->t('Priority'),
    //   '#options' => \Drupal\task_field\Plugin\Field\FieldType\ParametersItem::getPriorityOptions(),
    //   '#default_value' => $priority_value,
    //   '#empty_value' => '',
    // ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'task-field-parameters-elements';
    $element['#attached']['library'][] = 'task_field/task_field_parameters';

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
      if ($value['deadline'] === '') {
        $values[$delta]['deadline'] = NULL;
      }
      if ($value['deadline'] instanceof DrupalDateTime) {
        $values[$delta]['deadline'] = $value['deadline']->format('Y-m-d');
      }
      if ($value['status'] === '') {
        $values[$delta]['status'] = NULL;
      }
      if ($value['timer'] === '') {
        $values[$delta]['timer'] = NULL;
      }
      // if ($value['priority'] === '') {
      //   $values[$delta]['priority'] = NULL;
      // }
    }
    return $values;
  }

}
