<?php

/**
 * @file
 * Contains event_entity.page.inc.
 *
 * Page callback for Event entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event entity templates.
 *
 * Default template: event_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_event_entity(array &$variables) {
  // Fetch EventEntity Entity Object.
  $event_entity = $variables['elements']['#event_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
