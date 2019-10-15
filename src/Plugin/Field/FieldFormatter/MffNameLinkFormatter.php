<?php

namespace Drupal\mff\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
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
class MffNameLinkFormatter extends FileFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Use the media Name as the link text.');
    if ($this->getSetting('open_in_new_window')) {
      $summary[] = $this->t('Open in a new window.');
    }
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

      $options = [];
      if ($this->getSetting('open_in_new_window')) {
        $options['attributes'] = ['target' => '_blank'];
      }

      $elements[$delta] = [
        '#theme' => 'file_link',
        '#file' => $file,
        '#description' => $field_parent->get('name')->getString(),
        '#link_options' => $options,
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

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'open_in_new_window' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['open_in_new_window'] = [
      '#title' => $this->t('Open in a new window'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('open_in_new_window'),
    ];

    return $form;
  }

}
