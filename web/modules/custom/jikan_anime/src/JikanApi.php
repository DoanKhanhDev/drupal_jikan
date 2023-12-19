<?php

namespace Drupal\jikan_anime;

use DrupalCodeGenerator\InputOutput\IOAwareTrait;
use GuzzleHttp\Client;

/**
 *
 */
class JikanApi {

  use IOAwareTrait;

  const END_POINT = 'https://api.jikan.moe/v4/';

  /**
   * The client.
   *
   * @var \GuzzleHttp\Client
   */
  protected Client $client;

  /**
   * The construct Jikan API.
   *
   * @param \GuzzleHttp\Client $client
   *   The client.
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  /**
   * Get anime from jikan anime.
   *
   * @param int $page
   * The page.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getAnimates(int $page = 1) {
    $response = $this->client->request('GET',
      self::END_POINT . 'anime?page=' . $page);
    $response = json_decode($response->getBody()->getContents(), TRUE);

    return $response['data'];
  }

  /**
   * Get anime from jikan anime.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getPagination() {
    $response = $this->client->request('GET',
      self::END_POINT . 'anime');
    $response = json_decode($response->getBody()->getContents(), TRUE);

    return $response['pagination'];
  }

  /**
   * Get anime genre from jikan anime.
   *
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getAnimeGenres() {
    $response = $this->client->request('GET',
      self::END_POINT . 'genres/anime');
    $data = json_decode($response->getBody()->getContents(), TRUE);
    return $data['data'];
  }

}
