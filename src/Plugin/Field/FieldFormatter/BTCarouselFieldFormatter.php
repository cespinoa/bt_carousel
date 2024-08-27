<?php

declare(strict_types=1);

namespace Drupal\bt_carousel\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'bt_carousel_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "bt_carousel_field_formatter",
 *   label = @Translation("Bootstrap Toolbox Carousel"),
 *   field_types = {"entity_reference"},
 * )
 */
final class BTCarouselFieldFormatter extends FormatterBase {

  
  // Define constants for default settings
  private const SHOW_INDICATORS_DEFAULT = TRUE;
  private const SHOW_CONTROLS_DEFAULT = TRUE;
  private const SHOW_ALT_TEXT_DEFAULT = TRUE;
  private const SHOW_FULL_SCREEN_DEFAULT = FALSE;
  private const INTERVAL_DEFAULT = '5000';
  private const IMAGE_STYLE_DEFAULT = 'thumbnail';
  private const VIEW_MODE_DEFAULT = 'default';
  private const USE_IMAGE_ONLY_DEFAULT = FALSE;
  private const DARK_MODE_DEFAULT = FALSE;
  private const FADE_EFFECT_DEFAULT = FALSE;
  private const IMAGE_FIELD_DEFAULT = 'none';
  private const IMAGE_FIELD_STYLE_DEFAULT = 'default';
  private const TITLE_FIELD_DEFAULT = 'none';
  private const TEXT_FIELD_DEFAULT = '';
  private const SHOW_THUMBNAILS_DEFAULT = FALSE;
  private const THUMBNAIL_STYLE_DEFAULT = 'thumbnail';
  private const THUMBNAIL_FIELD_DEFAULT = NULL;
  private const TITLE_FIELD_TAG_DEFAULT = 'h2';
  private const TITLE_FIELD_STYLE_DEFAULT = NULL;
  private const TEXT_FIELD_LENGHT_DEFAULT = 200;
  private const TEXT_FIELD_STYLE_DEFAULT = NULL;
  private const ADD_LINK_DEFAULT = FALSE;
  private const TEXT_LINK_DEFAULT = '';
  private const TEXT_LINK_STYLE_DEFAULT = NULL;
  private const TEXT_AREA_STYLE_DEFAULT = NULL;
  
  protected $utilityService;
  
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * The eentity field manager.
   *
   * @var Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a BootstrapToolboxCarouselFieldFormatter object.
   *
   * @param string $pluginid
   *   The plugin ID for the formatter.
   * @param mixed $pluginDefinition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition
   *   The field definition.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label.
   * @param string $viewMode
   *   The view mode.
   * @param array $thirdPartySettings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityservice
   *    Custom services
   */
  public function __construct(
    $pluginid,
    $pluginDefinition,
    FieldDefinitionInterface $fieldDefinition,
    array $settings,
    $label,
    $viewMode,
    array $thirdPartySettings,
    EntityTypeManagerInterface $entityTypeManager,
    //~ EntityDisplayRepositoryInterface $entityDisplayRepository,
    //~ EntityFieldManagerInterface $entity_field_manager,
    UtilityServiceInterface $utilityservice
    ){
    parent::__construct($pluginid, $pluginDefinition, $fieldDefinition, $settings, $label, $viewMode, $thirdPartySettings);
    $this->entityTypeManager = $entityTypeManager;
    //~ $this->entityDisplayRepository = $entityDisplayRepository;
    //~ $this->entityFieldManager = $entity_field_manager;
    $this->utilityService = $utilityservice;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
      ContainerInterface $container,
      array $configuration,
      $pluginid,
      $pluginDefinition
    ){
    return new static(
      $pluginid,
      $pluginDefinition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      //~ $container->get('entity_display.repository'),
      //~ $container->get('entity_field.manager'),
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'show_indicators' => self::SHOW_INDICATORS_DEFAULT,
      'show_controls' => self::SHOW_CONTROLS_DEFAULT,
      'show_alt_text' => self::SHOW_ALT_TEXT_DEFAULT,
      'show_full_screen' => self::SHOW_FULL_SCREEN_DEFAULT,
      'interval' => self::INTERVAL_DEFAULT,
      'image_style' => self::IMAGE_STYLE_DEFAULT,
      'view_mode' => self::VIEW_MODE_DEFAULT,
      'use_image_only' => self::USE_IMAGE_ONLY_DEFAULT,
      'fade_effect' => self::FADE_EFFECT_DEFAULT,
      'dark_mode' => self::DARK_MODE_DEFAULT,
      'image_field' => self::IMAGE_FIELD_DEFAULT,
      'image_field_style' => self::IMAGE_FIELD_STYLE_DEFAULT,
      'text_field' => self::TEXT_FIELD_DEFAULT,
      'title_field' => self::TITLE_FIELD_DEFAULT,
      'show_thumbnails' => self::SHOW_THUMBNAILS_DEFAULT,
      'thumbnail_style' => self::THUMBNAIL_STYLE_DEFAULT,
      'thumbnail_field' => self::THUMBNAIL_FIELD_DEFAULT,
      'title_field_tag' => self::TITLE_FIELD_TAG_DEFAULT,
      'title_field_style' => self::TITLE_FIELD_STYLE_DEFAULT,
      'text_field_lenght' => self::TEXT_FIELD_LENGHT_DEFAULT,
      'text_field_style' => self::TEXT_FIELD_STYLE_DEFAULT,
      'add_link' => self::ADD_LINK_DEFAULT,
      'text_link' => self::TEXT_LINK_DEFAULT,
      'text_link_style' => self::TEXT_LINK_STYLE_DEFAULT,
      'text_area_style' => self::TEXT_AREA_STYLE_DEFAULT,
      
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $formState): array {

    $imageStyleOptions = $this->utilityService->getImageStyles();
    $targetType = $this->fieldDefinition->getSetting('target_type');

    $aditionalViewModeOptions = [
      'use_fields' => $this->t('Use fields'),
    ];
    
    if ($targetType === 'media') {
      $targetBundles = $this->fieldDefinition->getSetting('handler_settings')['target_bundles'] ?? [];
      if (in_array('image', $targetBundles, TRUE)) {
        $aditionalViewModeOptions['use_image'] = $this->t('Use image not media entity');
      }
    }

    
    $viewModeOptions = $this->utilityService->getViewModes(
      $targetType,
      TRUE,
      $aditionalViewModeOptions
    );

    $bundles = $this->fieldDefinition->getSetting('handler_settings')['target_bundles'] ?? [];
    $bundle = reset($bundles); 
    $fieldOptions = $this->utilityService->getBundleFields($targetType,$bundle);

    $form = [];
    
    
    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('View Mode'),
      '#default_value' => $this->getSetting('view_mode'),
      '#options' => $viewModeOptions,
    ];

    $form['image_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Image field'),
      '#options' => $fieldOptions,
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('image_field'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'], 
        ],
      ],
    ];

    $form['image_field_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image field style'),
      '#options' => $imageStyleOptions,
      '#default_value' => $this->getSetting('image_field_style'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][image_field]"]' => ['!empty' => TRUE],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
        ],
      ],
    ];

    $form['title_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Title field'),
      '#options' => $fieldOptions,
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('title_field'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'], 
        ],
      ],
    ];

    $form['title_field_tag'] = [
      '#type' => 'select',
      '#title' => $this->t('Title tag'),
      '#options' => $this->utilityService->getTagStyles(),
      '#default_value' => $this->getSetting('title_field_tag'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][title_field]"]' => ['!empty' => TRUE],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
        ],
      ],
    ];

    $form['title_field_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Title field style'),
      '#options' => $this->utilityService->getScopeListFiltered(['carousel_title_formatters']),
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('title_field_style'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][title_field]"]' => ['!empty' => TRUE],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
        ],
      ],
    ];
    
    $form['text_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Text field'),
      '#options' => $fieldOptions,
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('text_field'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'], 
        ],
      ],
    ];

    $form['text_field_lenght'] = [ 
      '#type' => 'textfield',
      '#title' => $this->t('Text field lenght'),
      '#default_value' => $this->getSetting('text_field_lenght'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][text_field]"]' => ['!empty' => TRUE],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
        ],
      ],
    ];

    $form['text_field_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Text field style'),
      '#options' => $this->utilityService->getScopeListFiltered(['carousel_text_formatters']),
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('text_field_style'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][text_field]"]' => ['!empty' => TRUE],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
        ],
      ],
    ];

    $form['add_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add link'),
      '#default_value' => $this->getSetting('add_link'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'], 
        ],
      ],
    ];

    $form['text_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text link'),
      '#default_value' => $this->getSetting('text_link'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][add_link]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['text_link_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Link style'),
      '#default_value' => $this->getSetting('text_link_style'),
      '#options' => $this->utilityService->getScopeListFiltered(['carousel_link_formatters']),
      '#empty_option' => 'None',
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_fields'],
          'and',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][add_link]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['text_area_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Caption area style'),
      '#options' => $this->utilityService->getScopeListFiltered(['carousel_text_area_formatters']),
      '#empty_option' => 'None',
      '#default_value' => $this->getSetting('text_area_style'),
      '#states' => [
        'visible' => [
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][title_field]"]' => ['!value' => 'empty'],
          'or',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][text_field]"]' => ['!value' => 'empty'],
          'or',
          ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][add_link]"]' => ['checked' => TRUE],
        ],
      ],
      
    ];

    $form['image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image Style'),
      '#default_value' => $this->getSetting('image_style'),
      '#options' => $imageStyleOptions,
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_image'],
        ],
      ],
    ];

    

    $form['show_alt_text'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show alt text'),
      '#default_value' => $this->getSetting('show_alt_text'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_image'],
        ],
      ],
    ];

    $form['show_full_screen'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Full screen mode'),
      '#default_value' => $this->getSetting('show_full_screen'),
      '#states' => [
        'visible' => [
          ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][view_mode]"]' => ['value' => 'use_image'],
        ],
      ],
    ];

    $form['show_indicators'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show indicators'),
      '#default_value' => $this->getSetting('show_indicators'),
    ];

    $form['show_controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show controls'),
      '#default_value' => $this->getSetting('show_controls'),
    ];

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval'),
      '#default_value' => $this->getSetting('interval'),
      '#min' => 0,
      '#step' => 500,
      '#description' => $this->t('Set the interval in milliseconds between slide transitions.'),
    ];

    $form['fade_effect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add fade effect'),
      '#default_value' => $this->getSetting('fade_effect'),
    ];

    $form['dark_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dark mode'),
      '#default_value' => $this->getSetting('dark_mode'),
    ];

    $form['show_thumbnails'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show thumbnails'),
      '#default_value' => $this->getSetting('show_thumbnails'),
    ];
    //~ kint($targetType);
    if ($targetType != 'media'){
      $form['thumbnail_field'] = [
        '#type' => 'select',
        '#title' => $this->t('Thumbnail field'),
        '#options' => $fieldOptions,
        '#default_value' => $this->getSetting('thumbnail_field'),
        '#states' => [
          'visible' => [
            ':input[name="fields['. $this->fieldDefinition->getName() .'][settings_edit_form][settings][show_thumbnails]"]' => ['checked' => TRUE], 
          ],
        ],
      ];
      $form['thumbnail_style'] = [
        '#type'=> 'select',
        '#title' => $this->t('Thumbnail image style'),
        '#options' => $imageStyleOptions,
        '#default_value' => $this->getSetting('thumbnail_style'),
        '#states' => [
          'visible' => [
            ':input[name="fields[' . $this->fieldDefinition->getName() . '][settings_edit_form][settings][show_thumbnails]"]' => ['checked' => TRUE],
          ],
        ],
      ];

    }



    
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $viewMode = $this->getSetting('view_mode');
    //~ kint($this->getSettings());
    if($viewMode == 'use_fields'){
      $fieldImage = $this->getSetting('image_field') ?? NULL;
      $fieldTitle = $this->getSetting('title_field') ?? NULL;
      $fieldText = $this->getSetting('text_field') ?? NULL;
      $fieldsString = 'Display with @view_mode ';
      $fieldsValues = ['@view_mode' => $viewMode];
      if($fieldImage){
        $fieldsString .= '@field_image ';
        $fieldsValues['@field_image'] = $fieldImage;
      }
      if($fieldTitle){
        $fieldsString .= '@field_title ';
        $fieldsValues['@field_title'] = $fieldTitle;
      }
      if($fieldText){
        $fieldsString .= '@field_text ';
        $fieldsValues['@field_text'] = $fieldText;
      }
      $summary[] = $this->t($fieldsString,$fieldsValues);
    } elseif($viewMode == 'use_image') {
      $imageStyle = $this->getSetting('image_style');
      $showAltText = $this->getSetting('show_alt_text') ?? NULL;
      $showFullScreen = $this->getSetting('show_full_screen') ?? NULL;
      
      $fieldsString = 'Display with @view_mode ';
      $fieldsValues = ['@view_mode' => $viewMode];

      if($imageStyle === 'default' ){
        $imageStyle = $this->t('Default');
      } else {
        $imageStyle = $this->entityTypeManager->getStorage('image_style')->load($this->getSetting('image_style'))->label();
      }
      $fieldsString .= ' using @image_style image style';
      $fieldsValues['@image_style'] = $imageStyle;

      $summary[] = $this->t($fieldsString,$fieldsValues);

      $fieldsString = '';
            
      if($showAltText){
        $fieldsString .= 'Showing alt-text as title';
      }
      if($showFullScreen){
        if($fieldsString){
          $fieldsString .= ' and display in full-screen mode';
        } else {
          $fieldsString = 'Display in full-screen mode';
        }
        
      }
      
      $summary[] = $this->t($fieldsString);
    } else {
      $summary[] = $this->t('Display with @view_mode ',['@view_mode' => $viewMode]);  
    }
    
    $summary_aux[] = $this->t('@value', ['@value' => $this->getSetting('show_indicators') ? $this->t('Show indicators') : NULL ]);
    $summary_aux[] = $this->t('@value', ['@value' => $this->getSetting('show_controls') ? $this->t('Show controls') : NULL]);
    $summary_aux[] = $this->t('@value', ['@value' => $this->getSetting('fade_effect') ? $this->t('Fade effect') : NULL ]);
    $summary_aux[] = $this->t('@value', ['@value' => $this->getSetting('dark_mode') ? $this->t('Dark mode') : NULL ]);
    $summary[] = implode(', ', $summary_aux);
    $summary[] = $this->t('Interval @value ms', ['@value' => $this->getSetting('interval')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $config = \Drupal::config('bt_carousel.settings');
    $selectedLibrary = $config->get('selected_library');
    $carouselId = 'carousel-' . uniqid();
    $settings = $this->getSettings();
    
    if($settings['interval'] == 0){
      $settings['interval'] = 86400000;
    }

    $viewMode = $this->getSetting('view_mode');
    
    
    if ( $viewMode === 'use_image') {
      $imageStyle = $this->getSetting('image_style');
      $images = [];
      $showThumbnails = $this->getSetting('show_thumbnails') ?? NULL;
      if($showThumbnails) {
        $thumbnailStyle = $this->getSetting('thumbnail_style');
        $thumbnails = [];
      }
      foreach ($items as $delta => $item) {
        $media = Media::load($item->target_id);
        
        if ($media instanceof MediaInterface && $media->bundle() === 'image') {
          $imageField = $media->get('field_media_image');

          if (!$imageField->isEmpty()) {
            $uri = $imageField->entity->getFileUri();
            
            if ($imageStyle !== 'default') {
              $uri = ImageStyle::load($imageStyle)->buildUri($uri);
            }
            
            $images[] = [
              'uri' => $uri,
              'alt' => $item->alt,
            ];
          }
          if($showThumbnails){
            $thumbnails[] = [
              'uri' => ImageStyle::load($imageStyle)->buildUri($imageField->entity->getFileUri()),
            ];
          }
        }
      }
      
      $elements[] = [
        '#theme' => 'bt_media_images_carousel',
        '#variables' => [
          'images' => $images,
          'thumbnails' => $thumbnails,
          'carousel_id' => $carouselId,
          'settings' => $this->getSettings(),
        ],
        '#attached' => [
          'library' => [
            $selectedLibrary,
          ],
        ],
      ];
      if ($this->getSetting('show_full_screen')) {
        $elements['#attached']['drupalSettings']['bt_carousel']['carousel_id'] = $carouselId;
        $elements['#attached']['drupalSettings']['bt_carousel']['interval'] = $this->getSetting('interval');
        $elements['#attached']['library'][] = 'bt_carousel/bt_carousel_full_screen';
      }
      
    } elseif ( $viewMode === 'use_fields'){
        $entityType = $this->fieldDefinition->getSetting('target_type');
        $fieldMedia = $settings['image_field'];
        $fieldTitle = $settings['title_field'];
        $fieldText = $settings['text_field'];
        $imageStyle = $settings['image_field_style'];
        $thumbnailStyle = $settings['thumbnail_style'];
        $text_field_lenght = $settings['text_field_lenght'];
        $showThumbnails = $settings['show_thumbnails'];

        $settings['text_area_style'] = $this->utilityService->getStyleById($settings['text_area_style']);
        $settings['title_field_style'] =  $this->utilityService->getStyleById($settings['title_field_style']);
        $settings['text_field_style'] = $this->utilityService->getStyleById($settings['text_field_style']);
        $settings['text_link_style'] = $this->utilityService->getStyleById($settings['text_link_style']);

        $slides = [];
        $thumbnails = [];
        foreach ($items as $delta => $item){
          $entity = $this->entityTypeManager->getStorage($entityType)->load($item->target_id);
          $mediaId = $entity->get($fieldMedia)->target_id;
          $title = $entity->get($fieldTitle)->value;
          $text = $entity->get($fieldText)->value;
          $text = text_summary($text, NULL, $text_field_lenght);
          $mediaUri = $this->utilityService->getMediaUriByMediaIdAndImageStyle($mediaId, $imageStyle);
          $link = NULL;
          if($settings['add_link']){
            $url = $entity->toUrl();
            $link_attributes = [
              'class' => $settings['text_link_style'],
            ];
            $link = Link::fromTextAndUrl($settings['text_link'], $url->setOptions(['attributes' => $link_attributes]));
            $link = $link->toRenderable();
            //~ kint(get_class_methods($link));
          }
          $slides[] = [
            'image' => $mediaUri,
            'title' => $title,
            'text' => $text,
            'link' => $link,
          ];
          if($showThumbnails){
            $thumbnails[] =[
              'uri' => $this->utilityService->getMediaUriByMediaIdAndImageStyle($mediaId, $thumbnailStyle),
            ];
          }
        }
        $settings['carousel_fade'] = $settings['fade_effect'] ? 'carousel-fade' : NULL;
        $settings['carousel_dark'] = $settings['dark_mode'] ? 'carousel-dark' : NULL;
        
        $elements[] = [
          '#theme' => 'bootstrap_toolbox_entities_as_fields_carousel',
          '#slides' => $slides,
          '#thumbnails' => $thumbnails,
          '#carousel_id' => $carouselId,
          '#settings' => $settings,
          '#attached' => [
            'library' => [
              $selectedLibrary,
            ],
          ],
        ];
        return $elements;
        
        

      
    } else {
      
      $entities = [];
      $thumbnails = [];
      $entityType = $this->fieldDefinition->getSetting('target_type');
      $bundles = $this->fieldDefinition->getSetting('handler_settings')['target_bundles'];
      $bundle = reset($bundles); 
      $viewMode = $this->getSetting('view_mode');
      $showThumbnails = $settings['show_thumbnails'] ?? FALSE;
      $imageStyle = $settings['thumbnail_style'] ?? FALSE;
      
      foreach ($items as $delta => $item) {
        $entities[$delta] = $this->utilityService->getRenderedEntity($entityType, $viewMode, $item->target_id);
        if($showThumbnails){
          if($entityType == 'media' && $bundle == 'image'){
            $mediaId = $item->target_id;
          }elseif($entityType == 'node'){
            $mediaId = $item->entity->get('field_media_image')->target_id;
          }elseif($entityType == 'media' && $bundle == 'remote_video'){
            $targetId = $item->entity->get('thumbnail')->target_id;
          }
          if($mediaId){
            $media = Media::load($mediaId);
            $mediaFieldName = $media->getSource()->getConfiguration()['source_field'];
            $imageField = $media->get($mediaFieldName);
            if (!$imageField->isEmpty()) {
              $thumbnails[] = [
                'uri' => ImageStyle::load($imageStyle)->buildUri($imageField->entity->getFileUri()),
              ];
            }
          }elseif($targetId){
            $file = File::load($targetId);
            if ($file) {
              $file_uri = $file->getFileUri();
              $thumbnails[] = [
                'uri' => $file_uri,
              ];
            }
          }
        }
      }

      $settings['carousel_fade'] = $settings['fade_effect'] ? 'carousel-fade' : NULL;
      $settings['carousel_dark'] = $settings['dark_mode'] ? 'carousel-dark' : NULL;
      
      $elements[] = [
        '#theme' => 'bt_entities_carousel',
        '#entities' => $entities,
        '#thumbnails' => $thumbnails,
        '#carousel_id' => $carouselId,
        '#settings' => $settings,
        '#attached' => [
          'library' => [
            $selectedLibrary,
          ],
        ],
      ];
    }
    
    return $elements;
  }

}
