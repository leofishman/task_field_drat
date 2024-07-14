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
    $element['task'] = $element + [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->task ?? NULL,
    ];
    // $element['status']
    // TODO CONTINUE HERE!!!!!!
    return $element;
  }

}
