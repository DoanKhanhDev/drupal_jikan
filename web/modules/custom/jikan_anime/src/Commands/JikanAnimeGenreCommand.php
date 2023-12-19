<?php

namespace Drupal\jikan_anime\Commands;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jikan_anime\JikanAnimeGenreInterface;
use Drupal\jikan_anime\JikanAnimeInterface;
use Drupal\jikan_anime\JikanApi;
use Drush\Commands\DrushCommands;

/**
 * Drush command file.
 */
class JikanAnimeGenreCommand extends DrushCommands {

  /**
   * The Jikan api.
   */
  protected JikanApi $jikanApi;

  /**
   * The entity type manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Jikan Storage.
   */
  protected EntityStorageInterface $jikanAnimeStorage;

  /**
   * The construct Jikan command.
   *
   * @param \Drupal\jikan_anime\JikanApi $jikan_api
   *   The Jikan api.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(JikanApi $jikan_api, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct();
    $this->jikanApi = $jikan_api;
    $this->entityTypeManager = $entity_type_manager;
    $this->jikanAnimeStorage = $entity_type_manager->getStorage('jikan_anime_genre');
  }

  /**
   * A Drush command to import anime from Jikan.
   *
   * @command jikan-anime:import:genre
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function importAnime(): void {
    // Get anime form Jikan api
    $genres = $this->jikanApi->getAnimeGenres();
    if (count($genres) === 0) {
      return;
    }

    $this->io()->progressStart(count($genres));
    foreach ($genres as $genre) {
      $this->io()->progressAdvance();
      $genre_entity = $this->jikanAnimeStorage->loadByProperties([
        'mal_id' => $genre['mal_id'],
      ]);

      $genre_entity = reset($genre_entity);
      if (!$genre_entity) {
        $this->createGenre($genre);
      }
      else {
        $this->updateGenre($genre_entity, $genre);
      }
    }

    $this->io()->progressFinish();
    $this->io()->success('Imported Entities!');
  }

  /**
   * create an anime.
   *
   * @param array $genre
   *   The anime from response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function createGenre(array $genre): void {
    // Mapping reference entity genre.
    $genre_entity = $this->jikanAnimeStorage->create([
      'mal_id' => $genre['mal_id'],
      'name' => $genre['name'],
      'json_value' => json_encode($genre),
      'created' => time(),
      'changed' => time(),
    ]);
    $genre_entity->save();
  }

  /**
   * Update an anime.
   *
   * @param \Drupal\jikan_anime\JikanAnimeGenreInterface $genre_entity
   *   The anime id.
   * @param array $genre
   *   The anime.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function updateGenre(JikanAnimeGenreInterface $genre_entity, array $genre): void {
    $genre_entity->set('mal_id', $genre['mal_id']);
    $genre_entity->set('name', $genre['name']);
    $genre_entity->set('json_value', json_encode($genre));
    $genre_entity->set('changed', time());
    $genre_entity->save();
  }

}
