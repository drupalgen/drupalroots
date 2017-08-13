<?php

namespace Drupal\genealogy_notes\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\genealogy_notes\Entity\GenealogyNotesInterface;

/**
 * Class GenealogyNotesController.
 *
 *  Returns responses for Notes routes.
 */
class GenealogyNotesController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Notes  revision.
   *
   * @param int $genealogy_notes_revision
   *   The Notes  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($genealogy_notes_revision) {
    $genealogy_notes = $this->entityManager()->getStorage('genealogy_notes')->loadRevision($genealogy_notes_revision);
    $view_builder = $this->entityManager()->getViewBuilder('genealogy_notes');

    return $view_builder->view($genealogy_notes);
  }

  /**
   * Page title callback for a Notes  revision.
   *
   * @param int $genealogy_notes_revision
   *   The Notes  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($genealogy_notes_revision) {
    $genealogy_notes = $this->entityManager()->getStorage('genealogy_notes')->loadRevision($genealogy_notes_revision);
    return $this->t('Revision of %title from %date', ['%title' => $genealogy_notes->label(), '%date' => format_date($genealogy_notes->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Notes .
   *
   * @param \Drupal\genealogy_notes\Entity\GenealogyNotesInterface $genealogy_notes
   *   A Notes  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(GenealogyNotesInterface $genealogy_notes) {
    $account = $this->currentUser();
    $langcode = $genealogy_notes->language()->getId();
    $langname = $genealogy_notes->language()->getName();
    $languages = $genealogy_notes->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $genealogy_notes_storage = $this->entityManager()->getStorage('genealogy_notes');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $genealogy_notes->label()]) : $this->t('Revisions for %title', ['%title' => $genealogy_notes->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all notes revisions") || $account->hasPermission('administer notes entities')));
    $delete_permission = (($account->hasPermission("delete all notes revisions") || $account->hasPermission('administer notes entities')));

    $rows = [];

    $vids = $genealogy_notes_storage->revisionIds($genealogy_notes);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\genealogy_notes\GenealogyNotesInterface $revision */
      $revision = $genealogy_notes_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $genealogy_notes->getRevisionId()) {
          $link = $this->l($date, new Url('entity.genealogy_notes.revision', ['genealogy_notes' => $genealogy_notes->id(), 'genealogy_notes_revision' => $vid]));
        }
        else {
          $link = $genealogy_notes->link($date);
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
              Url::fromRoute('entity.genealogy_notes.translation_revert', ['genealogy_notes' => $genealogy_notes->id(), 'genealogy_notes_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.genealogy_notes.revision_revert', ['genealogy_notes' => $genealogy_notes->id(), 'genealogy_notes_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.genealogy_notes.revision_delete', ['genealogy_notes' => $genealogy_notes->id(), 'genealogy_notes_revision' => $vid]),
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

    $build['genealogy_notes_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
