<?php

namespace Drupal\field_headline_group\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;


/**
 * Plugin implementation of the 'headline_default' formatter
 * 
 * @FieldFormatter(
 *   id = "headline_default",
 *   module = "field_monolith",
 *   label = @Translation("Headline Group (Complete)"),
 *   field_types = {
 *     "field_headline_group"
 *   }
 * );
 * 
 */

class HeadlineDefaultFormatter extends FormatterBase {

  public function settingsSummary() {
    $summary = [];
    $settings = $this->getSettings();
    $summary [] = t('Displays the headline group in a @tag.@class.',
      [
        '@tag' => $settings['headline_group_tag'], 
        '@class' => $settings['headline_group_class'],
      ]
    );

    return $summary;
  }

  public static function defaultSettings() {
    return [
      'headline_group_tag' => 'div',
      'headline_group_class' => 'headline-group',
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['headline_group_tag'] = [
      '#title' => t('Headline Group Tag'),
      '#type' => 'select',
      '#options' => [
        'div' => 'Div',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
      ],
      '#default_value' => $this->getSetting('headline_group_tag'),
    ];

    $element['headline_group_class'] = [
      '#title' => t('Headline Group Class'),
      '#type' => 'textfield',
      '#maxlength' => '64',
      '#default_value' => $this->getSetting('headline_group_class'),
    ];

    return $element;
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    $settings = $this->getSettings();

    foreach($items as $delta => $item) {

      $head = (empty($item->headline)) ? ($items->getParent()->get('title')->value) : $item->headline;
      $superhead = $item->superhead;
      $subhead = $item->subhead;

      if (empty($head) && empty($superhead) && empty($subhead)) continue;

      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => $settings['headline_group_tag'],
        '#attributes' => [
          'class' => $settings['headline_group_class'],
        ]
      ];

      if (!empty($superhead)) {
        $elements[$delta]['superhead'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => 'superhead'
          ],
          '#value' => $superhead,
        ];
      }

      if (!empty($head)) {
        $elements[$delta]['head'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => 'head'
          ],
          '#value' => $head,
        ];    
      }
      
      if (!empty($subhead)) {
        $elements[$delta]['subhead'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => 'subhead'
          ],
          '#value' => $subhead,
        ];
      }
    }

    return $elements;
  }
}
