<?php

/**
 * @file
 * Contains \Drupal\gallery\Controller\galleryController.
 */

namespace Drupal\gallery\Controller;

use Drupal\Core\Controller\ControllerBase;

class galleryController extends ControllerBase {

	public function content() {

		$data = [];

		/*
		* Get Title
		*/
		$query = db_query("
			SELECT d.title FROM aveyron.node_field_data d where d.nid = 62
		");

		$title = $query->fetchAll();
		$data['title'] = $title[0]->title;

		/*
		* Get first image on top
		*/
		$query = db_query("
			SELECT f.uri, s.field_image_on_top_alt,
			s.field_image_on_top_title
			from file_managed f
			join node__field_image_on_top s
			on f.fid = s.field_image_on_top_target_id
			join node_field_data d
			on d.nid = s.entity_id
			where d.nid = 62
		");

		$imageOnTop = $query->fetchAll();

		//$ens->uri = entity_load('image_style', '470_par_750')->buildUrl($ens->uri);
		$imageOnTop[0]->uri = file_create_url($imageOnTop[0]->uri);
		$data['imageOnTop'] = $imageOnTop[0];

		/*
		* Get ENS pictures's gallerie
		*/
		$query = db_query("
			SELECT f.uri, g.field_gallery_alt,
			g.field_gallery_title,d.nid, e.field_thematique_ens_target_id
			from file_managed f
			join node__field_gallery g
			on f.fid = g.field_gallery_target_id
			join node_field_data d
			on d.nid = g.entity_id
			join node__field_thematique_ens e
			on e.entity_id = d.nid
			where d.type = 'ens'
			ORDER BY RAND()
		");

		$picturesENS = $query->fetchAll();

		foreach ($picturesENS as $key => $pictureENS) {

			//Add alias path
			$path_alias = \Drupal::service('path.alias_manager')->getAliasByPath("/node/".$pictureENS->nid);
			$path_alias = ltrim($path_alias, '/');
			$pictureENS->url_alias = $path_alias;

			if($pictureENS->field_gallery_alt == 'faune' || $pictureENS->field_gallery_alt == 'flore' || $pictureENS->field_gallery_alt == 'paysage' || $pictureENS->field_gallery_alt == 'patrimoine'){

				$pictureENS->tag = $pictureENS->field_gallery_alt;

			}
			else{

				$query = db_query("
					SELECT d.title
					FROM aveyron.node_field_data d
					where d.nid = $pictureENS->field_thematique_ens_target_id
				");

				$tagENS = $query->fetchAll();

				$pictureENS->tag = strtolower($tagENS[0]->title);

			}

			// convert style
			$pictureENS->uri = entity_load('image_style', '900_par_600')->buildUrl($pictureENS->uri);

			// remove useless properties
			unset($pictureENS->field_thematique_ens_target_id);
			unset($pictureENS->nid);

		}

		// add to global var data
		$data['pictures'] = $picturesENS;

		/*
		* Get Taxons pictures's gallerie
		*/
		$query = db_query("
			SELECT f.uri, g.field_gallery_alt,
			g.field_gallery_title,d.nid, t.field_tag_value as tag
			from file_managed f
			join node__field_gallery g
			on f.fid = g.field_gallery_target_id
			join node_field_data d
			on d.nid = g.entity_id
			join node__field_tag t
			on t.entity_id = d.nid
			where d.type = 'taxon'
			ORDER BY RAND()
		");

		$picturesTaxons = $query->fetchAll();

		foreach ($picturesTaxons as $key => $pictureTaxon) {

			//Add alias path
			$path_alias = \Drupal::service('path.alias_manager')->getAliasByPath("/node/".$pictureTaxon->nid);
			$path_alias = ltrim($path_alias, '/');
			$pictureTaxon->url_alias = $path_alias;

			// convert style
			$pictureTaxon->uri = entity_load('image_style', '900_par_600')->buildUrl($pictureTaxon->uri);

			// remove useless properties
			unset($pictureTaxon->nid);

			// add in the main array
			array_push($data['pictures'], $pictureTaxon);
		}

		/*
		* Get ens video
		*/
		$query = db_query("
			SELECT v.field_video_ens_value FROM aveyron.node__field_video_ens v
		");

		$videos = $query->fetchAll();
		foreach ($videos as $key => $video) {

			$video->field_video_ens_value = explode("http://dai.ly/", $video->field_video_ens_value)[1];
			$video->tag = "video";

			// add in the main array
			array_push($data['pictures'], $video);

		}

		// random sort
		shuffle($data['pictures']);

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'gallery',
			'#data' => $data,
		);
	}
}