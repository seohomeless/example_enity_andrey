<?php

namespace Drupal\example\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\example\Entity\exampleInterface;

/**
 * Class exampleController.
 *
 *  Returns responses for Example routes.
 */
class exampleController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Example  revision.
   *
   * @param int $example_revision
   *   The Example  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($example_revision) {
    $example = $this->entityManager()->getStorage('example')->loadRevision($example_revision);
    $view_builder = $this->entityManager()->getViewBuilder('example');

    return $view_builder->view($example);
  }

  /**
   * Page title callback for a Example  revision.
   *
   * @param int $example_revision
   *   The Example  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($example_revision) {
    $example = $this->entityManager()->getStorage('example')->loadRevision($example_revision);
    return $this->t('Revision of %title from %date', ['%title' => $example->label(), '%date' => format_date($example->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Example .
   *
   * @param \Drupal\example\Entity\exampleInterface $example
   *   A Example  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(exampleInterface $example) {
    $account = $this->currentUser();
    $langcode = $example->language()->getId();
    $langname = $example->language()->getName();
    $languages = $example->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $example_storage = $this->entityManager()->getStorage('example');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $example->label()]) : $this->t('Revisions for %title', ['%title' => $example->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all example revisions") || $account->hasPermission('administer example entities')));
    $delete_permission = (($account->hasPermission("delete all example revisions") || $account->hasPermission('administer example entities')));

    $rows = [];

    $vids = $example_storage->revisionIds($example);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\example\exampleInterface $revision */
      $revision = $example_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $example->getRevisionId()) {
          $link = $this->l($date, new Url('entity.example.revision', ['example' => $example->id(), 'example_revision' => $vid]));
        }
        else {
          $link = $example->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.example.translation_revert', ['example' => $example->id(), 'example_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.example.revision_revert', ['example' => $example->id(), 'example_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.example.revision_delete', ['example' => $example->id(), 'example_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['example_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

  
    public function test(){
				
		$query = \Drupal::database()->select('example_field_data', 'nfd');
		$query->fields('nfd', ['title', 'url', 'description']);
		$result = $query->execute()->fetchAll();
		  
			  $variables['commenta_url'] = '/comment/reply/node/id ноды/comment/id комментария';

        return $result;		
	
    }
}
