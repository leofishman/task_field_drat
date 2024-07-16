<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'task_field_progress_key_value' formatter.
 *
 * @FieldFormatter(
 *   id = "task_field_progress_key_value",
 *   label = @Translation("Key-value"),
 *   field_types = {"task_field_progress"},
 * )
 */
final class ProgressKeyValueFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $element = [];

    foreach ($items as $delta => $item) {
      $table = [
        '#type' => 'table',
      ];

      // Priority.
      if ($item->priority) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Priority'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->priority,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Timer.
      if ($item->timer) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Timer'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->timer ? $this->t('Yes') : $this->t('No'),
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Status.
      if ($item->status) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Status'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->status,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Deadline.
      if ($item->deadline) {
        $date = DrupalDateTime::createFromFormat('Y-m-d', $item->deadline);
        $date_formatter = \Drupal::service('date.formatter');
        $timestamp = $date->getTimestamp();
        $formatted_date = $date_formatter->format($timestamp, 'long');
        $iso_date = $date_formatter->format($timestamp, 'custom', 'Y-m-d\TH:i:s') . 'Z';

        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Deadline'),
              ],
            ],
            [
              'data' => [
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
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      $element[$delta] = $table;
    }

    return $element;
  }

}
