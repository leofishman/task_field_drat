<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'task_field_task_field_default' field widget.
 *
 * @FieldWidget(
 *   id = "task_field_task_widget_default",
 *   label = @Translation("Task Field Default"),
 *   field_types = {"task_field_task"},
 * )
 */
final class TaskFieldDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = ['fo_widget' => 'bar'];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element['fo_widget'] = [
      '#type' => 'textfield',
      '#title' => $this->t('fo_widget'),
      '#default_value' => $this->getSetting('fo_widget'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('fo_widget: @fo_widget', ['@fo_widget' => $this->getSetting('fo_widget')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    $element['deadline'] = [
      '#type' => 'date',
      '#title' => $this->t('Deadline'),
      '#default_value' => isset($items[$delta]->deadline) ? $items[$delta]->deadline : '',
    ];

    $element['archive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Archive'),
      '#default_value' => isset($items[$delta]->archive) ? $items[$delta]->archive : 0,
    ];

    $element['timer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Timer'),
      '#default_value' => isset($items[$delta]->timer) ? $items[$delta]->timer : 0,
    ];

    $element['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => [
        'new' => $this->t('New'),
        'paused' => $this->t('Paused'),
        'in_progress' => $this->t('In Progress'),
        'completed' => $this->t('Completed'),
        'cancelled' => $this->t('Cancelled'),
      ],
      '#default_value' => isset($items[$delta]->status) ? $items[$delta]->status : '',
    ];

    $element['priority'] = [
      '#type' => 'select',
      '#title' => $this->t('Priority'),
      '#options' => [
        'none' => $this->t('none'),
        'low' => $this->t('Low'),
        'medium' => $this->t('Medium'),
        'high' => $this->t('High'),
        'urgent' => $this->t('Urgent'),
        'inmediate' => $this->t('Inmediate'),
      ],
      '#default_value' => isset($items[$delta]->priority) ? $items[$delta]->priority : '',
    ];

    return $element;
  }

}
