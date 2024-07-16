<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'task_field_progress_default' formatter.
 *
 * @FieldFormatter(
 *   id = "task_field_progress_default",
 *   label = @Translation("Default"),
 *   field_types = {"task_field_progress"},
 * )
 */
final class ProgressDefaultFormatter extends FormatterBase {

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
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->priority) {
        $element[$delta]['priority'] = [
          '#type' => 'item',
          '#title' => $this->t('Priority'),
          '#markup' => $item->priority,
        ];
      }

      $element[$delta]['timer'] = [
        '#type' => 'item',
        '#title' => $this->t('Timer'),
        '#markup' => $item->timer ? $this->t('Yes') : $this->t('No'),
      ];

      if ($item->status) {
        $element[$delta]['status'] = [
          '#type' => 'item',
          '#title' => $this->t('Status'),
          '#markup' => $item->status,
        ];
      }

      if ($item->deadline) {
        $date = DrupalDateTime::createFromFormat('Y-m-d', $item->deadline);
        // @DCG: Consider injecting the date formatter service.
        // @codingStandardsIgnoreStart
        $date_formatter = \Drupal::service('date.formatter');
        // @codingStandardsIgnoreStart
        $timestamp = $date->getTimestamp();
        $formatted_date = $date_formatter->format($timestamp, 'long');
        $iso_date = $date_formatter->format($timestamp, 'custom', 'Y-m-d') . 'Z';
        $element[$delta]['deadline'] = [
          '#type' => 'item',
          '#title' => $this->t('Deadline'),
          'content' => [
            '#theme' => 'time',
            '#text' => $formatted_date,
            '#html' => FALSE,
            '#attributes' => [
              'datetime' => $iso_date,
            ],
            '#cache' => [
              'contexts' => [
                'timezone',
              ],
            ],
          ],
        ];
      }

    }

    return $element;
  }

}
