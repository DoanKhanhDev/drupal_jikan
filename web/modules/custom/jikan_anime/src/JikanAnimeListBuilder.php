<?php declare(strict_types=1);

namespace Drupal\jikan_anime;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the jikan anime entity type.
 */
final class JikanAnimeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['title'] = $this->t('Title');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\jikan_anime\JikanAnimeInterface $entity */
    $row['id'] = $entity->id();
    $row['title'] = $entity->toLink();
    return $row + parent::buildRow($entity);
  }

}
