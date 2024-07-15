<?php

declare(strict_types=1);

namespace Drupal\task_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'task_field_parameters_table' formatter.
 *
 * @FieldFormatter(
 *   id = "task_field_parameters_table",
 *   label = @Translation("Table"),
 *   field_types = {"task_field_parameters"},
 * )
 */
final class ParametersTableFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $header[] = '#';
    $header[] = $this->t('Deadline');
    $header[] = $this->t('status');
    $header[] = $this->t('Timer');

    $table = [
      '#type' => 'table',
      '#header' => $header,
    ];

    foreach ($items as $delta => $item) {
      $row = [];

      $row[]['#markup'] = $delta + 1;

      if ($item->deadline) {
        $date = DrupalDateTime::createFromFormat('Y-m-d', $item->deadline);
        $date_formatter = \Drupal::service('date.formatter');
        $timestamp = $date->getTimestamp();
        $formatted_date = $date_formatter->format($timestamp, 'long');
        $iso_date = $date_formatter->format($timestamp, 'custom', 'Y-m-d\TH:i:s') . 'Z';
        $row[] = [
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
        ];
      }
      else {
        $row[]['#markup'] = '';
      }

      $row[]['#markup'] = $item->status;

      $row[]['#markup'] = $item->timer ? $this->t('Yes') : $this->t('No');

      $table[$delta] = $row;
    }

    return [$table];
  }

}
