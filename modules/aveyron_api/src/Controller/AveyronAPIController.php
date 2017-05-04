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

    $types = array('ens', 'taxon', 'thematique');
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
      if ($type == 'thematique')
        $result['thematic'] = $items;
      else
        $result[$type] = $items;
    }

    return new JsonResponse($result);
  }

  public function thematics( Request $request ) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'thematique');

    $itemIds = $query->execute();
    $entityManager = \Drupal::entityManager();
    $entities = $entityManager->getStorage('node')->loadMultiple($itemIds);

    $items = array();
    foreach ($entities as $entity) {
      $item = array();
      $item = array(
        "id" => (int) $entity->nid->value,
        "vid" => (int) $entity->vid->value,
        "title" => $entity->title->value,
        "icon" => $entity->field_icon_name->value
      );
      $items[] = $item;
    }

    $response = new JsonResponse($items);

    return $response;
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
        "thematic" => (int) $entity->field_thematique_ens->target_id,
        "startPoint" => $geom ? json_decode($geom->out('json'), true) : null,
        "descriptionShort" => substr($entity->body->summary, 0, 255),
        "poster" => array(),
        "thumbnail" => array()
      );

      /*
      * thematique
      */
      /*$query = db_query("
        SELECT d.nid
        FROM node__field_thematique_ens e
        join node_field_data d
        on d.nid = e.field_thematique_ens_target_id
        where e.entity_id = $itemId
      ");

      $thematique = $query->fetchAll();
      $item["thematic"] = (int) $thematique[0]->nid;*/

      /*
      * PDF
      */
      /*$query = db_query("
        SELECT f.uri, f.fid
        from file_managed f
        join node__field_pdf_ens g
        on f.fid = g.field_pdf_ens_target_id
        where g.entity_id = $itemId"
      );
      $pdf = $query->fetchAll();
      if(isset($pdf) && count($pdf) > 0){
        $pdf = file_create_url($pdf[0]->uri);
        $item["pdf"] = $pdf;
      }*/

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
      $item['poster'] = array(
        //"uri" => $poster[0]->uri,
        "url" => entity_load('image_style', '900_par_600')->buildUrl($poster[0]->uri),
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
      "thematic" => (int) $entity->field_thematique_ens->target_id,
      "info" => null,
      "reco" => $entity->field_recommandations->value,
      "pdf" => null,
      "startPoint" => json_decode($geom->out('json'), true),
      "trace" => json_decode($geomTrace->out('json'), true),
      "description" => $entity->body->value,
      "descriptionShort" => substr($entity->body->summary, 0, 255),
      "poster" => array(),
      "thumbnail" => array(),
      "gallery" => array(),
      "videos" => array(),
      "taxonIds" => array(),
      /*"events" => array(),*/
    );

    /*
    * thematique
    */
    /*$query = db_query("
      SELECT d.nid
      FROM node__field_thematique_ens e
      join node_field_data d
      on d.nid = e.field_thematique_ens_target_id
      where e.entity_id = $id
    ");

    $thematique = $query->fetchAll();
    $result["thematic"] = (int) $thematique[0]->nid;*/

    /*
    * PDF
    */
    $query = db_query("
      SELECT f.uri, f.fid
      from file_managed f
      join node__field_pdf_ens g
      on f.fid = g.field_pdf_ens_target_id
      where g.entity_id = $id"
    );
    $pdf = $query->fetchAll();
    if(isset($pdf) && count($pdf) > 0){
      $pdf = file_create_url($pdf[0]->uri);
      $result["pdf"] = $pdf;
    }

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
    /*$query = db_query("
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
    }*/



    $gallery = $entity->field_gallery;

    foreach ($gallery as $img) {
      $thematics = array('faune', 'flore', 'paysage', 'patrimoine');
      if (in_array($img->alt, $thematics))
        $tag = $img->alt;
      else
        $tag = $result['thematic'];

      //Buggy
      /*$img->uri = entity_load('image_style', '900_par_600')->buildUrl($img->uri);
      $result['gallery'][] = array(
        "url" => file_create_url($img->uri->value),
        "alt" => $img->alt,
        "title" => $img->title,
        "fid" => (int) $img->target_id,
        "tag" => $tag
      );*/
      $data = json_decode($serializer->serialize($img, 'json', ['plugin_id' => 'entity']));
      $data->tag = $tag;
      $data->target_id = (int) $data->target_id;
      //$data->url = entity_load('image_style', '900_par_600')->buildUrl($data->url);//$data->url;
      $result['gallery'][] = $data;
      //$result['gallery'][] = json_decode($serializer->serialize($img, 'json', ['plugin_id' => 'entity']));
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
    $chanVideo = substr_replace($chanVideo, "", -1)."&fields=id,thumbnail_480_url,title,tiny_url";
    // Example de call sur l'api Dailymotion : https://api.dailymotion.com/videos?ids=x5f5olp,x2c5umz&limit=30&fields=id,thumbnail_url,title,tiny_url
    $videoData = json_decode(file_get_contents($chanVideo), true);
    if (isset($videoData['list'])) {
      foreach ($videoData['list'] as &$video) {
        $video['link'] = $video['tiny_url'];
        $video['thumbnail'] = $video['thumbnail_480_url'];
      }
      $result['videos'] = $videoData['list'];
    }
    /*
    * Taxons
    */
    $taxa = $entity->field_taxa;
    foreach ($taxa as $taxon) {
      $result['taxonIds'][] = (int) $taxon->target_id;
    }

    /*
    * Get Info
    */
    $query = db_query("
      SELECT t.field_info_value
      FROM node__field_info t
      where t.entity_id = $id
    ");
    $info = $query->fetchAll();
    // add to global var data
    $result['info'] = $info[0]->field_info_value;

    /*
    * Get Info
    */
    $query = db_query("
      SELECT t.field_recommandations_value
      FROM node__field_recommandations t
      where t.entity_id = $id
    ");
    $recommandations = $query->fetchAll();

    if(isset($recommandations) && count($recommandations) > 0){
      // add to global var data
      $result['recommandations'] = $recommandations[0]->field_recommandations_value;
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

  public function ensEvents( $id, Request $request ) {
    $serializer = \Drupal::service('serializer');
    $entityManager = \Drupal::entityManager();

    $entity = $entityManager->getStorage('node')->load($id);
    if (!$entity->nid->value || $entity->getType() != 'ens') {
      return new Response(null, 404);
    }

    $eventIds = array();
    foreach ($entity->field_evenement_ens as $event) {
      $eventIds[] = $event->target_id;
    }
    $entities = $entityManager->getStorage('node')->loadMultiple($eventIds);
    $result = array();
    $serializer = \Drupal::service('serializer');
    foreach ($entities as $entity) {
      $poster = $entity->field_poster->entity;
      //$result[] = json_decode($serializer->serialize($entity, 'json', ['plugin_id' => 'entity']));
      $result[] = array(
        'id' => (int) $entity->nid->value,
        'created' => (int) $entity->created->value,
        'title' => $entity->title->value,
        'place' => $entity->field_place->value,
        'moment' => $entity->field_moment_evt->value,
        'poster' => file_create_url($poster->uri->value),
        'description' => $entity->body->value,
        'descriptionShort' => $entity->body->summary,
      );
    }

    usort($result, function($a, $b) {
      return $a['created'] > $b['created'] ? -1 : 1;
    });

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
        "description" => $entity->body->value,
        "descriptionShort" => $entity->body->summary,
        "poster" => array(),
        "thumbnail" => array(),
        "gallery" => array(),
        "category" => null
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
      $item["category"] = $categorie;

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

  public function events(Request $request) {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'event');

    $conditions = $request->get('conditions');
    if (is_string($conditions)) {
        $conditions = json_decode($conditions, true);
    }
    foreach ($conditions as $condition) {
      $query->condition($condition['field'], $condition['value'], $condition['operator'] ? $condition['operator'] : '=');
    }
    /*$today = new DateTime(date('Y-m-d'), new DateTimeZone('Europe/Paris'));
    $today->setTimezone(new DateTimeZone('UTC'));
    $query->condition('field_dates.end_value', $today->format('Y-m-d'), '>=');*/

    $sort = $request->get('sort');
    if (!$sort) {
      $sort = array(
        'field' => 'created',
        'direction' => 'ASC',
      );
    } elseif (is_string($sort)) {
      $sort = json_decode($sort, true);
    }
    $query->sort($sort['field'], $sort['direction']);

    try {
      $entityIds = $query->execute();
    } catch (Exception $e) {
      return new JsonResponse($e);
    }

    $entityManager = \Drupal::entityManager();
    $entities = $entityManager->getStorage('node')->loadMultiple($entityIds);

    $items = array();
    $serializer = \Drupal::service('serializer');
    /*
    $date = new DateTime('2000-01-01', new DateTimeZone('Pacific/Nauru'));
    echo $date->format('Y-m-d H:i:sP') . "\n";
    */
    foreach ($entities as $entity) {
      $poster = $entity->field_poster->entity;
      /*$from = new DateTime($entity->field_dates->value, new DateTimeZone('UTC'));
      $from->setTimezone(new DateTimeZone('Europe/Paris'));
      $to = new DateTime($entity->field_dates->end_value, new DateTimeZone('UTC'));
      $to->setTimezone(new DateTimeZone('Europe/Paris'));*/

      $item = array(
        'id' => (int) $entity->nid->value,
        'title' => $entity->title->value,
        'place' => $entity->field_place->value,
        'moment' => $entity->field_moment_evt->value,
        /*dateFrom => $from->format('c'),
        dateTo => $to->format('c'),
        displayHours => (bool) $entity->field_display_hours->value,*/
        'poster' => file_create_url($poster->uri->value),
        'description' => $entity->body->value,
        'descriptionShort' => $entity->body->summary,
        //enss => array(),
      );
      /*$enss = $entity->field_ens;
      foreach ($enss as $ens) {
        $ensData = json_decode($serializer->serialize($ens, 'json', ['plugin_id' => 'entity']));
        $item['enss'][] = (int) $ensData->target_id;
      }*/
      $items[] = $item;
    }

    return new JsonResponse($items);
  }

  public function medias( Request $request ) {
    $serializer = \Drupal::service('serializer');
    $entityManager = \Drupal::entityManager();

    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'ens');
    $ensIds = $query->execute();

    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'taxon');
    $taxonIds = $query->execute();

    $itemIds = array_merge($ensIds, $taxonIds);
    shuffle($itemIds);

    $entityManager = \Drupal::entityManager();
    $entities = $entityManager->getStorage('node')->loadMultiple($itemIds);
    $photos = array();
    $videoIds = array();
    foreach ($entities as $entity) {
      foreach ($entity->field_gallery as $img) {
        $imgUri = $img->entity->getFileUri();
        $photos[] = array(
          'poster' => entity_load('image_style', '900_par_600')->buildUrl($imgUri),
          'thumbnail' => entity_load('image_style', '200_par_200')->buildUrl($imgUri)
        );
      }
      if (isset($entity->field_video_ens)) {
        foreach ($entity->field_video_ens as $video) {
          $videoIds[] = array_pop(explode("dai.ly/", $video->value));
        }
      }
    }

    $videos = array();
    if (count($videoIds)) {
      $videoIds = array_unique($videoIds);
      $chanVideo = "https://api.dailymotion.com/videos?ids=". implode(',', $videoIds) ."&fields=id,thumbnail_480_url,title,tiny_url&limit=30";
      $response = json_decode(file_get_contents($chanVideo), true);
      $videos = $response['list'];
      //Map some values for standardization
      foreach ($videos as &$video) {
        $video['link'] = $video['tiny_url'];
        $video['thumbnail'] = $video['thumbnail_480_url'];
      }
    }

    return new JsonResponse(array(
      photos => $photos,
      videos => $videos
    ));
  }
}
