<?php declare(strict_types=1);

namespace Drupal\jikan_anime\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\jikan_anime\JikanAnimeInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\user\UserInterface;

/**
 * Defines the jikan anime entity class.
 *
 * @ContentEntityType(
 *   id = "jikan_anime",
 *   label = @Translation("Jikan anime"),
 *   label_collection = @Translation("Jikan animes"),
 *   label_singular = @Translation("jikan anime"),
 *   label_plural = @Translation("jikan animes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count jikan animes",
 *     plural = "@count jikan animes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\jikan_anime\JikanAnimeListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\jikan_anime\Form\JikanAnimeForm",
 *       "edit" = "Drupal\jikan_anime\Form\JikanAnimeForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" =
 *   "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "jikan_anime",
 *   admin_permission = "administer jikan_anime",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/jikan-anime",
 *     "add-form" = "/jikan-anime/add",
 *     "canonical" = "/jikan-anime/{jikan_anime}",
 *     "edit-form" = "/jikan-anime/{jikan_anime}/edit",
 *     "delete-form" = "/jikan-anime/{jikan_anime}/delete",
 *     "delete-multiple-form" = "/admin/content/jikan-anime/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.jikan_anime.settings",
 * )
 */
final class JikanAnime extends ContentEntityBase implements JikanAnimeInterface {

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

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['image'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Image'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['synopsis'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['rank'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Rank'))
      ->setDisplayConfigurable('view', TRUE);

    $fields['score'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Score'))
      ->setDisplayConfigurable('view', TRUE);

    $fields['popularity'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Popularity'))
      ->setDisplayConfigurable('view', TRUE);

    $fields['season'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Popularity'))
      ->setDisplayConfigurable('view', TRUE)
      ->setSettings([
        'allowed_values' => [
          'spring' => 'Spring',
          'summer' => 'Summer',
          'fall' => 'Fall',
        ],
      ]);

    $fields['genres'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Genre'))
      ->setSettings([
        'target_type' => 'jikan_anime_genre',
      ])
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('form', TRUE)
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
