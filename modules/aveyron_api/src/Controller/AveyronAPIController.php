<?php

/**
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */

namespace Drupal\aveyron_api\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AveyronAPIController extends ControllerBase {

  public function lists( Request $request ) {

    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'tour');
    $tourIds = $query->execute();

    $entityManager = \Drupal::entityManager();
    $tourEntities = $entityManager->getStorage('node')->loadMultiple($tourIds);

    $serializer = \Drupal::service('serializer');
    $tours = array();
    $taxonIds = array();
    foreach ($tourEntities as $tourEntity) {
      $tour = array(
        id => (int) $tourEntity->nid->value,
        vid => (int) $tourEntity->vid->value,
        title => $tourEntity->title->value,
        thumbnail => file_create_url($tourEntity->field_thumbnail->entity->uri->value),
        poster => file_create_url($tourEntity->field_poster->entity->uri->value),
        descriptionShort => substr($tourEntity->field_description->value, 0, 255),
        taxons => array(),
      );
      $taxons = $tourEntity->get('field_taxon');
      foreach ($taxons as $taxon) {
        $taxonId = (int) $taxon->target_id;
        $tour['taxons'][] = $taxonId;
        if (!in_array($taxonIds, $taxonId)) {
          $taxonIds[] = $taxonId;
        }
      }
      //$tour = $serializer->serialize($tourEntity, 'json', ['plugin_id' => 'entity']);
      $tours[] = $tour;
    }

    $taxons = array();
    $taxonEntities = $entityManager->getStorage('node')->loadMultiple($taxonIds);
    foreach ($taxonEntities as $taxonEntity) {
      $taxonId = $taxonEntity->nid->value;
      $taxons[$taxonId] = (int) $taxonEntity->vid->value; //$serializer->serialize($taxonEntity, 'json', ['plugin_id' => 'entity']);
    }

    return new JsonResponse(array(
      tours => $tours,
      taxons => $taxons,
    ));
  }

  public function tour( $id, Request $request ) {

    $entityManager = \Drupal::entityManager();
    $entity = $entityManager->getStorage('node')->load($id);

    if (!$entity->nid->value || $entity->getType() != 'tour') {
      return new Response(null, 404);
    }

    $result = array(
      id => (int) $entity->nid->value,
      vid => (int) $entity->vid->value,
      title => $entity->title->value,
      thumbnail => file_create_url($entity->field_thumbnail->entity->uri->value),
      poster => file_create_url($entity->field_poster->entity->uri->value),
      description => $entity->field_description->value,
      descriptionShort => substr($entity->field_description->value, 0, 255),
      taxons => array(),
    );
    $taxons = $entity->field_taxon;
    foreach ($taxons as $taxon) {
      $result['taxons'][] = (int) $taxon->target_id;
    }

    return new JsonResponse($result);
  }

  public function taxons( Request $request ) {

    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'taxon');

    $conditions = json_decode($request->get('conditions'), true);
    foreach ($conditions as $key => $condition) {
      $query->condition($condition['field'], $condition['value'], $condition['operator'] ? $condition['operator'] : '=');
    }

    try {
      $taxonsIds = $query->execute();
    } catch (Exception $e) {
      return new JsonResponse($e);
    }

    $entityManager = \Drupal::entityManager();
    $taxonEntities = $entityManager->getStorage('node')->loadMultiple($taxonsIds);

    $taxons = array();
    foreach ($taxonEntities as $taxonEntity) {
      $taxons[] = array(
        id => (int) $taxonEntity->nid->value,
        vid => (int) $taxonEntity->vid->value,
        title => $taxonEntity->title->value,
        thumbnail => file_create_url($taxonEntity->field_thumbnail->entity->uri->value),
        poster => file_create_url($taxonEntity->field_poster->entity->uri->value),
        descriptionShort => substr($taxonEntity->field_description->value, 0, 255),
      );
    }

    return new JsonResponse($taxons);
  }
}
