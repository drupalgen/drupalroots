<?php

/**
 * @file
 * Contains genealogy_notes.page.inc.
 *
 * Page callback for Notes entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Notes templates.
 *
 * Default template: genealogy_notes.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_genealogy_notes(array &$variables) {
  // Fetch GenealogyNotes Entity Object.
  $genealogy_notes = $variables['elements']['#genealogy_notes'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
