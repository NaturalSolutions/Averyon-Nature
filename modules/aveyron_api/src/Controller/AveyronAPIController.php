<?php

/**
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */

/*
Exemple :
http://192.168.0.114/aveyron-pierre/api/enss?conditions=[{%22field%22:%22nid%22,%22value%22:[2,4],%22operator%22:%22in%22}]
*/

namespace Drupal\aveyron_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\geoPHP;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AveyronAPIController extends ControllerBase {

  public function vidsAction( Request $request ) {
    $entityManager = \Drupal::entityManager();

    $types = array('ens', 'taxon');
    $result = array();
    foreach ($types as $type) {
      $query = \Drupal::entityQuery('node');
      $query->condition('status', 1);
      $query->condition('type', $type);
      $ids = $query->execute();
      $entities = $entityManager->getStorage('node')->loadMultiple($ids);
      $items = array();
      foreach ($entities as $entity) {
        $items[] = array(
          "id" => (int) $entity->nid->value,
          "vid" => (int) $entity->vid->value
        );
      }
      $result[$type] = $items;
    }

    return new JsonResponse($result);
  }

  public function enss( Request $request ) {

    $fullItemIds = $request->get('fullItemIds');
    if(isset($fullItemIds)){
      if (is_string($fullItemIds)) {
          $fullItemIds = json_decode($fullItemIds, true);
      }
      foreach ($fullItemIds as $fullItemId) {
        if (is_string($fullItemId)) {
          $fullItemId = (int) $fullItemId;
        }
      }
    }

    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'ens');
    $conditions = $request->get('conditions');
    if(isset($conditions)){
      if (is_string($conditions)) {
          $conditions = json_decode($conditions, true);
      }
      foreach ($conditions as $key => $condition) {
        $query->condition($condition['field'], $condition['value'], $condition['operator'] ? $condition['operator'] : '=');
      }
    }


    $itemIds = $query->execute();

    $entityManager = \Drupal::entityManager();
    $entities = $entityManager->getStorage('node')->loadMultiple($itemIds);

    $serializer = \Drupal::service('serializer');
    $items = array();
    $taxonIds = array();
    foreach ($entities as $entity) {
      $item = array();
      //$poster = $entity->field_poster->entity;
      $geom = \geoPHP::load($entity->field_start_trace->value,'wkt');
      $itemId = (int) $entity->nid->value;
      $item = array(
        "id" => $itemId,
        "vid" => (int) $entity->vid->value,
        "title" => $entity->title->value,
        "thematique" => array(),
        "startTrace" => $geom ? json_decode($geom->out('json'), true) : null,
        "descriptionShort" => substr($entity->body->summary, 0, 255),
        "poster" => array(),
        "thumbnail" => array()
      );

      /*
      * thematique
      */
      $query = db_query("
        SELECT d.title
        FROM node__field_thematique_ens e
        join node_field_data d
        on d.nid = e.field_thematique_ens_target_id
        where e.entity_id = $itemId
      ");

      $thematique = $query->fetchAll();
      $thematique = $thematique[0]->title;
      $item["thematique"] = $thematique;

      /*
      * Thumb - 1st image of gallery with special style
      */
      $query = db_query("
        SELECT f.uri, f.fid, g.field_gallery_alt,
        g.field_gallery_title
        from file_managed f
        join node__field_gallery g
        on f.fid = g.field_gallery_target_id
        where g.entity_id = $itemId limit 1"
      );

      $thumbnail = $query->fetchAll();
      $thumbnail[0]->uri = entity_load('image_style', '200_par_200')->buildUrl($thumbnail[0]->uri);
      $item['thumbnail'] = array(
        "url" => $thumbnail[0]->uri,
        "alt" => $thumbnail[0]->field_gallery_alt,
        "title" => $thumbnail[0]->field_gallery_title,
        "fid" => (int) $thumbnail[0]->fid
      );

      /*
      * Poster - 1st image of gallery with special style
      */
      $query = db_query("
        SELECT f.uri, f.fid, g.field_gallery_alt,
        g.field_gallery_title
        from file_managed f
        join node__field_gallery g
        on f.fid = g.field_gallery_target_id
        where g.entity_id = $itemId limit 1"
      );
      $poster = $query->fetchAll();
      $poster[0]->uri = entity_load('image_style', '900_par_600')->buildUrl($poster[0]->uri);
      $item['poster'] = array(
        "url" => $poster[0]->uri,
        "alt" => $poster[0]->field_gallery_alt,
        "title" => $poster[0]->field_gallery_title,
        "fid" => (int) $poster[0]->fid
      );

      if(isset($fullItemIds)){
        if (in_array($itemId, $fullItemIds)) {
          $item['description'] = $entity->body->value;
          $item['taxonIds'] = array();
          $taxa = $entity->field_taxa;
          foreach ($taxa as $taxon) {
            $item['taxonIds'][] = (int) $taxon->target_id;
          }
        }
      }

      $items[] = $item;
    }

    $response = new JsonResponse($items);

    //$response->headers->set('Access-Control-Allow-Origin', '*');

    return $response;
  }

  public function ens( $id, Request $request ) {

    $entityManager = \Drupal::entityManager();
    $entity = $entityManager->getStorage('node')->load($id);

    if (!$entity->nid->value || $entity->getType() != 'ens') {
      return new Response(null, 404);
    }
    $serializer = \Drupal::service('serializer');

    $geom = \geoPHP::load($entity->field_start_trace->value,'wkt');
    $geomTrace = \geoPHP::load($entity->field_trace->value,'wkt');
    $result = array(
      "id" => (int) $entity->nid->value,
      "vid" => (int) $entity->vid->value,
      "title" => $entity->title->value,
      "thematique" => null,
      "startPoint" => json_decode($geom->out('json'), true),
      "trace" => json_decode($geomTrace->out('json'), true),
      "description" => $entity->body->value,
      "descriptionShort" => substr($entity->body->summary, 0, 255),
      "poster" => array(),
      "thumbnail" => array(),
      "gallery" => array(),
      "videos" => array(),
      "taxonIds" => array(),
    );

    /*
    * thematique
    */
    $query = db_query("
      SELECT d.title
      FROM node__field_thematique_ens e
      join node_field_data d
      on d.nid = e.field_thematique_ens_target_id
      where e.entity_id = $id
    ");

    $thematique = $query->fetchAll();
    $thematique = $thematique[0]->title;
    $result["thematique"] = $thematique;

    /*
    * Poster - 1st image of gallery with special style
    */
    $query = db_query("
      SELECT f.uri, f.fid, g.field_gallery_alt,
      g.field_gallery_title
      from file_managed f
      join node__field_gallery g
      on f.fid = g.field_gallery_target_id
      where g.entity_id = $id limit 1"
    );
    $poster = $query->fetchAll();
    $poster[0]->uri = entity_load('image_style', '900_par_600')->buildUrl($poster[0]->uri);
    $result['poster'] = array(
      "url" => $poster[0]->uri,
      "alt" => $poster[0]->field_gallery_alt,
      "title" => $poster[0]->field_gallery_title,
      "fid" => (int) $poster[0]->fid
    );

    /*
    * Gallery - 1st picture is used for like main presentation image
    */
    $query = db_query("
      SELECT f.uri, f.fid, g.field_gallery_alt,
      g.field_gallery_title
      from file_managed f
      join node__field_gallery g
      on f.fid = g.field_gallery_target_id
      where g.entity_id = $id"
    );

    $gallery = $query->fetchAll();

    foreach ($gallery as $img) {

      if($img->field_gallery_alt == 'faune' || $img->field_gallery_alt == 'flore' || $img->field_gallery_alt == 'paysage' || $img->field_gallery_alt == 'patrimoine') $tag = $img->field_gallery_alt;
      else $tag = $thematique;

      //TODO
      $img->uri = entity_load('image_style', '900_par_600')->buildUrl($img->uri);
      $result['gallery'][] = array(
        "url" => $img->uri,
        "alt" => $img->field_gallery_alt,
        "title" => $img->field_gallery_title,
        "fid" => (int) $img->fid,
        "tag" => $tag
      );
    }

    /*
    * Thumb - 1st image of gallery with special style
    */
    $query = db_query("
      SELECT f.uri, f.fid, g.field_gallery_alt,
      g.field_gallery_title
      from file_managed f
      join node__field_gallery g
      on f.fid = g.field_gallery_target_id
      where g.entity_id = $id limit 1"
    );

    $thumbnail = $query->fetchAll();
    $thumbnail[0]->uri = entity_load('image_style', '200_par_200')->buildUrl($thumbnail[0]->uri);
    $result['thumbnail'] = array(
      "url" => $thumbnail[0]->uri,
      "alt" => $thumbnail[0]->field_gallery_alt,
      "title" => $thumbnail[0]->field_gallery_title,
      "fid" => (int) $thumbnail[0]->fid
    );

    /*
    * Videos
    */
    $query = db_query("
      SELECT v.field_video_ens_value FROM node__field_video_ens v where v.entity_id = $id
    ");

    $videos = $query->fetchAll();
    $chanVideo = "https://api.dailymotion.com/videos?ids=";
    foreach ($videos as $video) {
      $video->field_video_ens_value = explode("http://dai.ly/", $video->field_video_ens_value)[1];
      $chanVideo.=$video->field_video_ens_value.",";
    }
    $chanVideo = substr_replace($chanVideo, "", -1)."&fields=id,thumbnail_url,title,tiny_url";
    // Example de call sur l'api Dailymotion : https://api.dailymotion.com/videos?ids=x5f5olp,x2c5umz&limit=30&fields=id,thumbnail_url,title,tiny_url
    $result['videos'] = json_decode(file_get_contents($chanVideo));


    $taxa = $entity->field_taxa;
    foreach ($taxa as $taxon) {
      $result['taxonIds'][] = (int) $taxon->target_id;
    }

    return new JsonResponse($result);
  }

  public function ensQuiz( $id, Request $request ) {
    $serializer = \Drupal::service('serializer');
    $entityManager = \Drupal::entityManager();

    $entity = $entityManager->getStorage('node')->load($id);
    if (!$entity->nid->value || $entity->getType() != 'ens') {
      return new Response(null, 404);
    }

    $questionIds = array();
    foreach ($entity->field_quiz as $question) {
      $questionIds[] = $question->target_id;
    }
    $entities = $entityManager->getStorage('node')->loadMultiple($questionIds);
    $result = array(
      "questions" => array()
    );
    foreach ($entities as $question) {
      $answers = array();
      foreach ($question->field_answers as $answer) {
        $answers[] = $answer->value;
      }
      $result['questions'][] = array(
        "id" => (int) $question->nid->value,
        /*"vid" => (int) $question->vid->value, useless for quiz */
        "title" => $question->title->value,
        "poster" => entity_load('image_style', '900_par_600')->buildUrl($question->field_poster->entity->uri->value),
        "goodAnswer" => (int) $question->field_good_answer[0]->value,
        "answers" => $answers,
      );
    }

    return new JsonResponse($result);
  }

  public function taxa( Request $request ) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'taxon');

    $conditions = $request->get('conditions');
    if(isset($conditions)){
      if (is_string($conditions)) {
          $conditions = json_decode($conditions, true);
      }
      foreach ($conditions as $key => $condition) {
        $query->condition($condition['field'], $condition['value'], $condition['operator'] ? $condition['operator'] : '=');
      }
    }

    try {
      $taxaIds = $query->execute();
    } catch (Exception $e) {
      return new JsonResponse($e);
    }

    $entityManager = \Drupal::entityManager();
    $entities = $entityManager->getStorage('node')->loadMultiple($taxaIds);

    $taxa = array();
    $serializer = \Drupal::service('serializer');
    foreach ($entities as $entity) {
      $poster = $entity->field_poster->entity;
      $item = array(
        "id" => (int) $entity->nid->value,
        "vid" => (int) $entity->vid->value,
        "title" => $entity->title->value,
        "description" => $entity->body->summary,
        "descriptionShort" => substr($entity->body->summary, 0, 255),
        "poster" => array(),
        "thumbnail" => array(),
        "gallery" => array(),
        "categorie" => array()
      );

      $id = $entity->nid->value;

      /*
      * Poster - 1st image of gallery with special style
      */
      $query = db_query("
        SELECT f.uri, f.fid, g.field_gallery_alt,
        g.field_gallery_title
        from file_managed f
        join node__field_gallery g
        on f.fid = g.field_gallery_target_id
        where g.entity_id = $id limit 1"
      );
      $poster = $query->fetchAll();
      $poster[0]->uri = entity_load('image_style', '900_par_600')->buildUrl($poster[0]->uri);
      $item['poster'] = array(
        "url" => $poster[0]->uri,
        "fid" => (int) $poster[0]->fid
      );

      /*
      * Thumb - 1st image of gallery with special style
      */
      $query = db_query("
        SELECT f.uri, f.fid, g.field_gallery_alt,
        g.field_gallery_title
        from file_managed f
        join node__field_gallery g
        on f.fid = g.field_gallery_target_id
        where g.entity_id = $id limit 1"
      );

      $thumbnail = $query->fetchAll();
      $thumbnail[0]->uri = entity_load('image_style', '200_par_200')->buildUrl($thumbnail[0]->uri);
      $item['thumbnail'] = array(
        "url" => $thumbnail[0]->uri,
        "fid" => (int) $thumbnail[0]->fid
      );


      /*
      * Gallery - 1st picture is used for like main presentation image
      */
      $query = db_query("
        SELECT f.uri, f.fid, g.field_gallery_alt,
        g.field_gallery_title
        from file_managed f
        join node__field_gallery g
        on f.fid = g.field_gallery_target_id
        where g.entity_id = $id"
      );

      $gallery = $query->fetchAll();
      foreach ($gallery as $img) {
        //TODO
        $img->uri = entity_load('image_style', '900_par_600')->buildUrl($img->uri);
        $item['gallery'][] = array(
          "url" => $img->uri,
          "fid" => (int) $img->fid
        );
      }

      /*
      * Catégorie
      */
      $query = db_query("
        SELECT t.field_tag_value FROM aveyron.node__field_tag t
        where t.entity_id = $id
      ");

      $categorie = $query->fetchAll();
      $categorie = $categorie[0]->field_tag_value;
      $item["categorie"] = $categorie;

      /*
      * Audio
      */
      $query = db_query("
        SELECT f.uri, f.fid
        from file_managed f
        join node__field_audio g
        on f.fid = g.field_audio_target_id
        where g.entity_id = $id
      ");

      $audio = $query->fetchAll();

      if(isset($audio) && count($audio) > 0){

        $audio = file_create_url($audio[0]->uri);
        $item["audio"] = $audio;

      }
      /*
      $audio = $audio[0]->field_tag_value;
      $item["audio"] = $audio;
      */

      $taxa[] = $item;
    }

    return new JsonResponse($taxa);
  }
}
