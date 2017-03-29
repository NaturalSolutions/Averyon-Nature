<?php

/**
 * @file
 * Contains \Drupal\list_ens\Controller\list_ensController.
 */

namespace Drupal\list_ens\Controller;

use Drupal\Core\Controller\ControllerBase;

class list_ensController extends ControllerBase {

	public function content() {

		$data = [];


		/*
		* FOLD 1 - Get image on top
		*/
		$query = db_query("
			SELECT f.uri, s.field_image_on_top_alt,
			s.field_image_on_top_title
			from file_managed f
			join node__field_image_on_top s
			on f.fid = s.field_image_on_top_target_id
		");

		$pictureOnTop = $query->fetchAll();

		//$ens->uri = entity_load('image_style', '470_par_750')->buildUrl($ens->uri);
		$pictureOnTop[0]->uri = file_create_url($pictureOnTop[0]->uri);
		$data['imageOnTop'] = $pictureOnTop[0];

		/*
		* FOLD 1 - Get thematiques
		*/
		$query = db_query("
			SELECT n.title
			FROM node_field_data n
			where n.type = 'thematique'
		");

		$thematiques = $query->fetchAll();
		$data['thematiques'] = $thematiques;

		/*
		* FOLD 2 - Get ENS
		*/
		$query = db_query(
			"
			select f.uri, d.title as thematique, s.field_poster_alt, s.field_poster_title, s.entity_id, s.revision_id from node_revision a
			join node_revision__field_poster s
			on s.revision_id = a.vid
			join file_managed f
			on f.fid = s.field_poster_target_id
			join node__field_thematique_ens t
			on t.revision_id = s.revision_id
			join node_field_data d
			on d.nid = t.field_thematique_ens_target_id
			where a.revision_timestamp = (select max(z.revision_timestamp) from node_revision z where z.nid = a.nid);
			"
		);

		$picturesFold2 = $query->fetchAll();

		foreach ($picturesFold2 as $key => $picture) {

			//Add alias path
			$path_alias = \Drupal::service('path.alias_manager')->getAliasByPath("/node/".$picture->entity_id, $langcode);
			$path_alias = ltrim($path_alias, '/');
			$picture->url_alias = $path_alias;

			//Convert uri to the good style
			if( $key == 0 )	$picture->uri = entity_load('image_style', '1000_par_700')->buildUrl($picture->uri);
			else $picture->uri = entity_load('image_style', '760_par_400')->buildUrl($picture->uri);

			//  get title
			//	SELECT title FROM aveyron.node_field_data where vid = 1;

			$query2 = \Drupal::database()->select('node_field_data', 'n');
			$query2->addField('n', 'title');
			$query2->condition('n.nid', $picture->entity_id);
			$titleEnsFold2 = $query2->execute()->fetchAll();
			$titleEnsFold2 = $titleEnsFold2[0]->title;
			$picturesFold2[$key]->title = $titleEnsFold2;

			//	Get GPS point of ENS :
			//	SELECT t.field_start_trace_lat, t.field_start_trace_lon
			//	FROM aveyron.node_revision__field_start_trace t
			//	where t.revision_id = 10;

			$query3 = \Drupal::database()->select('node_revision__field_start_trace', 't');
			$query3->addField('t', 'field_start_trace_lat');
			$query3->addField('t', 'field_start_trace_lon');
			$query3->condition('t.revision_id', $picture->revision_id);
			$GPSEnsFold2 = $query3->execute()->fetchAll();

			if(isset($GPSEnsFold2[0])){

				$GPSEnsFold2 = $GPSEnsFold2[0]->field_start_trace_lat.';'.$GPSEnsFold2[0]->field_start_trace_lon;
				$picturesFold2[$key]->gps = $GPSEnsFold2;

			}

		}

		$data['ensFold2'] = $picturesFold2;

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'list_ens',
			'#data' => $data,
		);

	}

}