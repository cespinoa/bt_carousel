<?php

namespace Drupal\bt_carousel\Plugin\views\row;

use Drupal\views\Plugin\views\row\RowPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Unicode;

/**
 * Plugin de fila personalizado para mostrar los resultados de la vista.
 *
 * @ViewsRow(
 *   id = "bt_carousel_row",
 *   title = @Translation("Carousel Row"),
 *   help = @Translation("Renderiza resultados con mi lógica personalizada."),
 *   theme = "views_view_row_bt_carousel",
 *   display_types = {"normal"},
 *   usesFields = TRUE
 * )
 */
class BTCarouselRow extends RowPluginBase {

    


  /**
   * Provide a form for setting options.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state){

    $form['msg'] = [
      '#markup' => $this->t('This plugin has not configurations. Options are set with Bootstrap Toolbox Carousel config form'),
    ];
    
  }

  /**
   * {@inheritdoc}
   */
  public function usesFields() {
    return TRUE; // $this->usesFields;
  }

  /**
   * {@inheritdoc}
   */
  public function render($row) {

    $options = $this->view->style_plugin->options;
    
    $output = [];

    $entity = $row->_entity;
    $viewField = $this->view->field;

    $fieldTitle = $options['carousel_title'] != 'none' ? $options['carousel_title'] : NULL;
    if($fieldTitle){
      $value = $entity->get($fieldTitle)->value;
      $class = $viewField[$fieldTitle]->elementClasses($row);
      $element = $viewField[$fieldTitle]->elementType();
      $linkToEntity = $viewField[$fieldTitle]->options['settings']['link_to_entity'];
      $entityUrl = $entity->toUrl();
      $output['title'] = $this->createRenderArrayTitle($value, $class, $element, $linkToEntity, $entityUrl);
    }
    
    $fieldBody = $options['carousel_text'] != 'none' ? $options['carousel_text'] : NULL;
    if($fieldBody){
      $value = $entity->get($fieldBody)->value;
      $value = text_summary($value ?? '', NULL, 200);
      $class = $viewField[$fieldBody]->elementClasses($row);
      $element = $viewField[$fieldBody]->elementType();
      $output['body'] = $this->createRenderArrayBody($value, $class, $element);
    }

    $fieldLink = $options['carousel_link'] != 'none' ? $options['carousel_link'] : NULL;
    if($fieldLink){
      $url = $entity->toUrl()->toString();
      $value = $viewField[$fieldLink]->original_value;
      $class = $viewField[$fieldLink]->elementClasses($row);
      $output['link'] = $this->createRenderArrayLink($value, $class, $url);
    }
    
    $fieldImage = $options['carousel_image'] != 'none' ? $options['carousel_image'] : NULL;
    if($fieldImage){
      $fieldOptions = $viewField[$fieldImage]->options;
      $viewMode = $fieldOptions['settings']['view_mode'];
      $class = $viewField[$fieldImage]->elementClasses($row);
      $field = $entity->get($fieldImage);
      if (!$field->isEmpty()) {
        $mediaEntity = $field->entity;
        $renderedEntity = \Drupal::entityTypeManager()
            ->getViewBuilder('media')
            ->view($mediaEntity, $viewMode);
        $renderedEntity['#attributes'] = ['class' => [$class]];
        $output['image'] = $renderedEntity;
      }
    }
    
    return $output;
  }

  /**
   * @param string $value
   * @param string $class
   * @param string $element
   * @param string $linkToEntity
   * @param string $entityUrl
   *
   * @return array
   *
   **/
  public function createRenderArrayTitle($value, $class, $element, $linkToEntity, $entityUrl): array {
    if($linkToEntity){
      $link = [
        '#type' => 'link',
        '#url' => $entityUrl,
        '#title' => $value,    
        '#attributes' => [
          'class' => $class,   
        ],
      ];
       return [
          '#type' => 'html_tag',
          '#tag' => $element,
          '#attributes' => [
              'class' => $class,
          ],
          'content' => $link,  
      ];
    } else {
      return [
        '#type' => 'html_tag',
        '#tag' => $element,  
        '#value' => $value,  
        '#attributes' => [
          'class' => $class, 
        ],
      ];
    }
  }

  /**
   * @param string $value
   * @param string $class
   * @param string $element
   *
   * @return array
   *
   **/
  public function createRenderArrayBody($value, $class, $element): array{
    return [
      '#type' => 'html_tag',
      '#tag' => $element,
      '#value' => $value,
      '#attributes' => [
        'class' => $class,
      ],
    ];
  }

  /**
   * @param string $value
   * @param string $class
   * @param string $url
   *
   * @return array
   *
   **/
  public function createRenderArrayLink($value, $class, $url): array{
    return [
      '#type' => 'html_tag', 
      '#tag' => 'a',
      '#value' => $value,
      '#attributes' => [
        'class' => $class,
        'href' => $url,
      ],
    ];
  }


}
