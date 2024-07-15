<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Task' formatter.
 *
 * @FieldFormatter(
 *   id = "task_field_task_formatter_default",
 *   label = @Translation("Task"),
 *   field_types = {"task_field_task"},
 * )
 */
final class TaskFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = ['fo_settings' => 'active'];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements['fo_settings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fo_settings'),
      '#default_value' => $this->getSetting('fo_settings'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Fo_settings: @fo_settings', ['@fo_settings' => $this->getSetting('fo_settings')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    dump($items);
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#task' => $item->task,
        '#deadline' => DrupalDateTime::createFromTimestamp($item->deadline)->format('Y-m-d'),
        '#archive' => $item->archive ? $this->t('Yes') : $this->t('No'),
        '#timer' => $item->timer ? $this->t('Yes') : $this->t('No'),
        '#status' => $item->status,
        '#priority' => $item->priority,
        '#comments' => $item->comments,
        '#theme' => 'task_field',
      ];
    }
    return $element;
  }

}
