<?php

namespace Drupal\example;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Example entities.
 *
 * @ingroup example
 */
class exampleListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
	$header['title'] = $this->t('Title');
	$header['description'] = $this->t('Description');
	$header['url'] = $this->t('URL');
	return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\example\Entity\example */
    $row['id'] = $entity->id();
	$row['title'] = $entity->title->value;
	$row['description'] = $entity->description->value;
    $row['url'] = $entity->url->value;
    return $row + parent::buildRow($entity);
  }

}
