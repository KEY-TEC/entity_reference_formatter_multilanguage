<?php

namespace Drupal\entity_reference_formatter_multilanguage\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'entity reference rendered entity' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_entity_view_multilanguage",
 *   label = @Translation("Rendered published entity in current language"),
 *   description = @Translation("Display the referenced entities rendered by entity_view()."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceEntityFormatterMultiLanguage extends EntityReferenceEntityFormatter {
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode) {
    $origin_langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $language = ConfigurableLanguage::load($origin_langcode);
    
    $entities = parent::getEntitiesToView($items, $langcode);
    foreach ($entities as $delta => $entity) {
      $published = TRUE;
      $same_language = TRUE;
      if ($fallback != "" && $entity->hasTranslation($fallback)) {
        $langcode = $fallback;
      }
      else {
        $langcode = $origin_langcode;
      }
      if ($entity instanceof NodeInterface) {
        $same_language = $entity->hasTranslation($langcode);
        $has_origin_language = $entity->hasTranslation($origin_langcode);
        if($same_language){
          $entity = $entity->getTranslation($langcode);
          if ($has_origin_language) {
            $entity = $entity->getTranslation($origin_langcode);
          }
          $entities[$delta] = $entity;
        }
        if ($has_origin_language) {
          $published = $entity->getTranslation($origin_langcode)->isPublished();
        } else {
          $published = $entity->isPublished();
        }
      }
      if (!$published || !$same_language) {
        unset($entities[$delta]);
      }
    }

    return $entities;
  }

}
