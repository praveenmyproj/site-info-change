<?php
/**
 * @file
 * Contains \Drupal\site_config\Controller\ .
 */

namespace Drupal\site_config\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Utility\Unicode;

class SiteConfig extends ControllerBase {
  /**
   *
   * Implements the JSON for the provided APIKEY and Node ID.
   *
   */
  public function index($apikey, $nid) {
    if (Unicode::strtolower($apikey) != Unicode::strtolower(\Drupal::config('system.site')->get('siteapikey')) || (!empty($nid) && !is_numeric($nid))) {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
    $nids = \Drupal::entityQuery('node')->condition('type','page')->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $array = [];
    if(empty($nid)) {
      foreach($nodes as $nid => $node) {
        if (Unicode::strtolower($apikey) == Unicode::strtolower($node->field_api_key->value)) {
          $array[$nid] = ['title' => $node->title->value, 'body' => $node->body->value];
        }
      }
    }
    else if (!empty($nodes[$nid])) {
      if (Unicode::strtolower($apikey) == Unicode::strtolower($nodes[$nid]->field_api_key->value)) {
        $array = ['title' => $nodes[$nid]->title->value, 'body' => $nodes[$nid]->body->value];
      }
    }
    if (!empty($array)) {
      return new JsonResponse($array);
    }
    else {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
  }
}
