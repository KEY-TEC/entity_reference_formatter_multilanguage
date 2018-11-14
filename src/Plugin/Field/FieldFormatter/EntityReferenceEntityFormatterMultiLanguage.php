<?php

namespace Drupal\entity_reference_formatter_multilanguage\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\social_media_links\Plugin\SocialMediaLinks\Platform\Drupal;

/**
 * Plugin implementation of the 'entity reference rendered entity' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_entity_view_multilanguage",
 *   label = @Translation("Only render published entities in the current language"),
 *   description = @Translation("Display the referenced entities rendered by
 *   entity_view()."), field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceEntityFormatterMultiLanguage extends EntityReferenceEntityFormatter {

  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode) {
    $entities = parent::getEntitiesToView($items, $langcode);
    $current_language = \Drupal::languageManager()->getCurrentLanguage();
    foreach ($entities as $delta => $entity) {
      // Hide entities in differnt language.
      if ($entity->language() != $current_language) {
        unset($entities[$delta]);
      }

      // Hide non published entities. isPublished is part of trait.
      // So we need to check the
      if (method_exists($entity, 'isPublished') && !$entity->isPublished()) {
        unset($entities[$delta]);
      }

    }
    return $entities;
  }
}
