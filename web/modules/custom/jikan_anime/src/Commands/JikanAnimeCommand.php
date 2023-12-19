<?php

namespace Drupal\jikan_anime\Commands;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\jikan_anime\JikanAnimeInterface;
use Drupal\jikan_anime\JikanApi;
use Drush\Commands\DrushCommands;

/**
 * Drush command file.
 */
class JikanAnimeCommand extends DrushCommands {

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
    $this->jikanAnimeStorage = $entity_type_manager->getStorage('jikan_anime');
  }

  /**
   * A Drush command to import anime from Jikan.
   *
   * @command jikan-anime:import:anime
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function importAnime(): void {
    // Get anime form Jikan api
    $animates = [];
    $this->output->writeln('Get animates form Jikan API...');
    $get_pagination = $this->jikanApi->getPagination();
//    $get_pagination['last_visible_page'] = 1;
    $this->io()->progressStart($get_pagination['last_visible_page']);
    for ($page = 1; $page <= $get_pagination['last_visible_page']; $page++) {
      $this->io()->progressAdvance();
      if ($page % 3 === 0) {
        sleep(2);
      }
      $animates = array_merge($animates, $this->jikanApi->getAnimates($page));
    }
    $this->io()->progressFinish();
    $this->io()->success('Get animates form Jikan API completed!');

    // Import Entity.
    $this->output->writeln('Import entities...');
    if (count($animates) === 0) {
      return;
    }

    $this->io()->progressStart(count($animates));
    foreach ($animates as $anime) {
      $this->io()->progressAdvance();
      $anime_entity = $this->jikanAnimeStorage->loadByProperties([
        'mal_id' => $anime['mal_id'],
      ]);

      $anime_entity = reset($anime_entity);
      if (!$anime_entity) {
        $this->createAnime($anime);
      }
      else {
        $this->updateAnime($anime_entity, $anime);
      }
    }

    $this->io()->progressFinish();
    $this->io()->success('Imported Entities!');
  }

  /**
   * create an anime.
   *
   * @param array $anime
   *   The anime from response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function createAnime(array $anime): void {
    // Mapping reference entity genre.
    $genre_ids = [];
    foreach ($anime['genres'] as $genre) {
      $genre_ids[] = $genre['mal_id'];
    }

    $anime_entity = $this->jikanAnimeStorage->create([
      'mal_id' => $anime['mal_id'],
      'title' => $anime['title'],
      'image' => $anime['images']['jpg']['image_url'],
      'synopsis' => $anime['synopsis'],
      'rank' => $anime['rank'],
      'score' => $anime['score'],
      'popularity' => $anime['popularity'],
      'season' => $anime['season'],
      'genres' => $genre_ids,
      'json_value' => json_encode($anime),
      'created' => time(),
      'changed' => time(),
    ]);
    $anime_entity->save();
  }

  /**
   * Update an anime.
   *
   * @param \Drupal\jikan_anime\JikanAnimeInterface $anime_entity
   *   The anime id.
   * @param array $anime
   *   The anime.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function updateAnime(JikanAnimeInterface $anime_entity, array $anime): void {
    $genre_ids = [];
    foreach ($anime['genres'] as $genre) {
      $genre_ids[] = $genre['mal_id'];
    }
    $anime_entity->set('mal_id', $anime['mal_id']);
    $anime_entity->set('title', $anime['title']);
    $anime_entity->set('image', $anime['images']['jpg']['image_url']);
    $anime_entity->set('synopsis', $anime['synopsis']);
    $anime_entity->set('rank', $anime['rank']);
    $anime_entity->set('score', $anime['score']);
    $anime_entity->set('popularity', $anime['popularity']);
    $anime_entity->set('season', $anime['season']);
    $anime_entity->set('genres', $genre_ids);
    $anime_entity->set('json_value', json_encode($anime));
    $anime_entity->set('changed', time());
    $anime_entity->save();
  }

}
