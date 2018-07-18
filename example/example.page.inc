<?php

/**
 * @file
 * Contains example.page.inc.
 *
 * Page callback for Example entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Example templates.
 *
 * Default template: example.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_example(array &$variables) {
  // Fetch example Entity Object.
  $example = $variables['elements']['#example'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
