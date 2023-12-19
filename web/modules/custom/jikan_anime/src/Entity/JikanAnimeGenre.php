<?php declare(strict_types=1);

namespace Drupal\jikan_anime\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\jikan_anime\JikanAnimeInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\user\UserInterface;

/**
 * Defines the jikan anime entity class.
 *
 * @ContentEntityType(
 *   id = "jikan_anime_genre",
 *   label = @Translation("Jikan anime genre"),
 *   label_collection = @Translation("Jikan anime genres"),
 *   label_singular = @Translation("Jikan anime genre"),
 *   label_plural = @Translation("Jikan anime genres"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Jikan anime genres",
 *     plural = "@count Jikan anime genres",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\jikan_anime\JikanAnimeListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "jikan_anime_genre",
 *   admin_permission = "administer jikan_anime_genre",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/jikan-anime",
 *   },
 *   field_ui_base_route = "entity.jikan_anime_genre.settings",
 * )
 */
final class JikanAnimeGenre extends ContentEntityBase implements JikanAnimeInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['mal_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Id'))
      ->setDisplayConfigurable('form', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDisplayConfigurable('view', TRUE);

    $fields['json_value'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('json'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the jikan anime was last edited.'));

    return $fields;
  }

}
