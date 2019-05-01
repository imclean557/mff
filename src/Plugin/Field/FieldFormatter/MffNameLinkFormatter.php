<?php

namespace Drupal\mff\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;
use Drupal\Core\Field\FieldItemListInterface;


/**
 * Plugin implementation of the 'Name field link text' formatter.
 *
 * @FieldFormatter(
 *   id = "mff_name_link_formatter",
 *   label = @Translation("Name field link text"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class MffNameLinkFormatter extends FileFormatterBase  {
  
 /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Uses the media Name field as the link text.');
    return $summary;
  }
  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $file) {
      $item = $file->_referringItem;

      $field_parent = $item->getEntity();

      $elements[$delta] = [
        '#theme' => 'file_link',
        '#file' => $file,
        '#description' => $field_parent->get('name')->getString(),
        '#cache' => [
          'tags' => $file->getCacheTags(),
        ],
      ];
      // Pass field item attributes to the theme function.
      if (isset($item->_attributes)) {
        $elements[$delta] += ['#attributes' => []];
        $elements[$delta]['#attributes'] += $item->_attributes;
        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }
    }

    return $elements;
 
  }

}
