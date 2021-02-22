<?php

namespace Drupal\mff\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\DescriptionAwareFileFormatterBase;

/**
 * Plugin implementation of the 'Name field link text' formatter.
 *
 * @FieldFormatter(
 *   id = "mff_description_formatter",
 *   label = @Translation("File field description text"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class MffDescriptionFormatter extends DescriptionAwareFileFormatterBase {

    /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['use_description_as_link_text'] = FALSE;
    return $settings;
  }
  
 /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('The file field description text.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    // Repurpose field to determine whether this field should be a link or not.
    $form['use_description_as_link_text'] = [
      '#title' => $this
        ->t('Link description to file.'),
      '#description' => $this
        ->t('Link the description text to the file.'),
      '#type' => 'checkbox',
      '#default_value' => $this
        ->getSetting('use_description_as_link_text'),
    ];
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $file) {
      $item = $file->_referringItem;
      if ($this->getSetting('use_description_as_link_text')) {
        $elements[$delta] = [
          '#theme' => 'file_link',
          '#file' => $file,
          '#description' => $item->description,
          '#cache' => [
            'tags' => $file->getCacheTags(),
          ],
        ];
      }
      else {
        $elements[$delta] = [
          '#type' => 'processed_text',
          '#text' => $item->description,
          '#langcode' => $item->getLangcode(),
        ];
      }

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
