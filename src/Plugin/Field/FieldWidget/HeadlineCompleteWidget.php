<?php

namespace Drupal\field_headline_group\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field_headline_group\HeadlineGroupItemInterface;


/** 
 * Plugin implementation of the 'headline_complete' widget
 * 
 * @FieldWidget(
 *   id = "headline_complete",
 *   label = "Headline Group (all fields)",
 *   field_types = {
 *     "field_headline_group"
 *   }
 * )
 * 
 */

class HeadlineCompleteWidget extends WidgetBase {
  
  /* Settings form is for form settings wooo
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $element
  }
  */

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    

    if ($this->supportsSuperhead()) {
      $element['superhead'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Superhead'),
        '#placeholder' => NULL,
        '#default_value' => isset($items[$delta]->superhead) ? $items[$delta]->superhead : NULL,
        '#maxlength' => 255,      
      ];
    }
    
    $currentTitle = $items->getParent()->get('title')->value;
    
    switch ($this->titleBehavior()) {
      case HeadlineGroupItemInterface::HG_OVERRIDE:
        $headline_placeholder = (empty($currentTitle)) ? $this->t('The main headline') : $currentTitle;
        $headline_disabled = false;
        $headline_description = "If you do not provide a headline, the title will be used instead.";
      break;
      case HeadlineGroupItemInterface::HG_PROHIBIT:
        $headline_placeholder = (empty($currentTitle)) ? $this->t('Use the entity title.') : $currentTitle;
        $headline_disabled = true;
        $headline_description = "The headline is automatically provided by the title.";
      break;
      default: // Inc. HG_BLANK
        $headline_placeholder = $this->t('The main headline');
        $headline_disabled = false;
        $headline_description = NULL;
    }

    $element['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#placeholder' => $headline_placeholder,
      '#default_value' => isset($items[$delta]->superhead) ? $items[$delta]->headline : NULL,
      '#maxlength' => 255,
      '#disabled' => $headline_disabled,
      '#description' => $headline_description,
    ];
    
    if ($this->supportsSubhead()) {
      $element['subhead'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Subhead'),
        '#placeholder' => NULL,
        '#default_value' => isset($items[$delta]->subhead) ? $items[$delta]->subhead : NULL,
        '#maxlength' => 255,      
      ];
    }

    $element += [
      '#type' => 'fieldset',
    ];
    

    return $element;

  }

  /**
   * Indicates enabled support for superheads
   *
   * @return bool
   *   Returns TRUE if the HeadlineGroupItem field is configured to support superheads,
   *   otherwise FALSE.
   */
  protected function supportsSuperhead() {
    $support = $this->getFieldSetting('include_superhead');
    return (bool) (HeadlineGroupItemInterface::HG_SUPERHEAD === $support);
  }

  /**
   * Indicates enabled support for subheads
   *
   * @return bool
   *   Returns TRUE if the HeadlineGroupItem field is configured to support subheads,
   *   otherwise FALSE.
   */
  protected function supportsSubhead() {
    $support = $this->getFieldSetting('include_subhead');
    return (bool) (HeadlineGroupItemInterface::HG_SUBHEAD === $support);
  }

  /** 
   *  Returns the preference for headline treatment.
   */

  protected function titleBehavior() {
    return $this->getFieldSetting('title_behavior');
  }
}