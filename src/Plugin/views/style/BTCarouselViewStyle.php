<?php

declare(strict_types=1);

namespace Drupal\bt_carousel\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Bootstrap Toolbox Carousel style plugin.
 *
 * @ViewsStyle(
 *   id = "bt_carousel_view_style",
 *   title = @Translation("Bootstrap Toolbox Carousel"),
 *   help = @Translation("Display content as a Bootstrap carousel."),
 *   theme = "views_view_bt_carousel_view_style",
 *   display_types = {"normal"},
 * )
 */
final class BootstrapToolboxCarouselViewStyle extends StylePluginBase {

  // Default values for options.
  private const DEFAULT_SHOW_INDICATORS = TRUE;
  private const DEFAULT_SHOW_CONTROLS = TRUE;
  private const DEFAULT_INTERVAL = 6000;
  private const DEFAULT_IMAGE = NULL;
  private const DEFAULT_TITLE = NULL;
  private const DEFAULT_TEXT = NULL;
  private const DEFAULT_LINK = NULL;
  private const DEFAULT_DARK_MODE = FALSE;
  private const DEFAULT_FADE_EFFECT = FALSE;


  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesRowClass = TRUE;

  

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['show_indicators'] = ['default' => self::DEFAULT_SHOW_INDICATORS];
    $options['show_controls'] = ['default' => self::DEFAULT_SHOW_CONTROLS];
    $options['interval'] = ['default' => self::DEFAULT_INTERVAL];
    $options['fade_effect'] = ['default' => self::DEFAULT_FADE_EFFECT];
    $options['dark_mode'] = ['default' => self::DEFAULT_DARK_MODE];
    $options['carousel_image'] = ['default' => self::DEFAULT_IMAGE];
    $options['carousel_title'] = ['default' => self::DEFAULT_TITLE];
    $options['carousel_text'] = ['default' => self::DEFAULT_TEXT];
    $options['carousel_link'] = ['default' => self::DEFAULT_LINK];
    return $options;
  }
   
  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $formstate): void {
    $form['show_indicators'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show indicators'),
      '#default_value' => $this->options['show_indicators'],
    ];

    $form['show_controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show controls'),
      '#default_value' => $this->options['show_controls'],
    ];

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval'),
      '#default_value' => $this->options['interval'],
      '#description' => $this->t('Set the interval in milliseconds between slide transitions.'),
      '#min' => 1000,
      '#step' => 500,
    ];

    $form['fade_effect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add fade effect'),
      '#default_value' => $this->options['fade_effect'],
    ];

    $form['dark_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dark mode'),
      '#default_value' => $this->options['dark_mode'],
    ];



    // Check if the view is configured to show fields instead of content.
    $style_options = $this->displayHandler->getOption('row');
    if (isset($style_options['type']) && $style_options['type'] === 'bt_carousel_row') {
      
      // Retrieve the fields available in the view.
      $fields = $this->displayHandler->getOption('fields');
      $fieldOptions = [];

      // Populate the select options with the field titles.
      foreach ($fields as $fieldName => $fieldInfo) {
        $fieldOptions[$fieldName] = !empty($fieldInfo['label']) ? $fieldInfo['label'] : $fieldInfo['field'];
      }

      $form['carousel_image'] = [
        '#type' => 'select',
        '#title' => $this->t('Carousel image'),
        '#description' => $this->t('Select the field to use as the image for the carousel.'),
        '#options' => $fieldOptions,
        '#default_value' => $this->options['carousel_image'],
        '#empty_option' => $this->t('None'),
      ];

      $form['carousel_title'] = [
        '#type' => 'select',
        '#title' => $this->t('Carousel title'),
        '#description' => $this->t('Select the field to use as the title for the carousel.'),
        '#options' => $fieldOptions,
        '#default_value' => $this->options['carousel_title'],
        '#empty_option' => $this->t('None'),
      ];

      $form['carousel_text'] = [
        '#type' => 'select',
        '#title' => $this->t('Carousel text'),
        '#description' => $this->t('Select the field to use as the text for the carousel.'),
        '#options' => $fieldOptions,
        '#default_value' => $this->options['carousel_text'],
        '#empty_option' => $this->t('None'),
      ];

      $form['carousel_link'] = [
        '#type' => 'select',
        '#title' => $this->t('Carousel link'),
        '#description' => $this->t('Select the field to use as the link for the carousel.'),
        '#options' => $fieldOptions,
        '#default_value' => $this->options['carousel_link'],
        '#empty_option' => $this->t('None'),
      ];
      
    }
    else {
      $form['carousel_field_info'] = [
        '#markup' => $this->t('You can customize your carousel if you select display Carousel rows.'),
      ];
    }
  









    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $formstate): void {
    // Validate interval.
    $interval = $formstate->getValue(['style_options', 'interval']);
    if (!is_numeric($interval) || $interval <= 0) {
      $formstate->setErrorByName('interval', $this->t('The interval must be a positive number.'));
    }

    // Save the validated values.
    $this->options['show_indicators'] = $formstate->getValue(['style_options', 'show_indicators']);
    $this->options['show_controls'] = $formstate->getValue(['style_options', 'show_controls']);
    $this->options['interval'] = $formstate->getValue(['style_options', 'interval']);
  }

}
