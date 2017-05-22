<?php

/**
 * @file
 * Contains \Drupal\carto\Controller\cartoController.
 */

namespace Drupal\carto\Controller;

use Drupal\Core\Controller\ControllerBase;

class cartoController extends ControllerBase {

	public function content() {

		$data = [];

		/*
		* Get Title
		*/
		$query = db_query("
			SELECT d.title FROM node_field_data d where d.nid = 283
		");

		$title = $query->fetchAll();
		$data['title'] = $title[0]->title;

		/*
		* FOLD 1 - Get image on top
		*/
		$query = db_query("
			SELECT f.uri, s.field_poster_alt,
			s.field_poster_title
			from file_managed f
			join node__field_poster s
			on f.fid = s.field_poster_target_id
			join node_field_data d
			on d.nid = s.entity_id
			where d.nid = 283
		");

		$imageOnTop = $query->fetchAll();

		//$ens->uri = entity_load('image_style', '470_par_750')->buildUrl($ens->uri);
		$imageOnTop[0]->uri = file_create_url($imageOnTop[0]->uri);
		$data['imageOnTop'] = $imageOnTop[0];

		/*
		* Get Title
		*/
		$query = db_query("
			SELECT i.field_iframe_de_la_carte_value FROM node__field_iframe_de_la_carte i where i.entity_id = 283
		");

		$iframe = $query->fetchAll();
		$data['iframe'] = $iframe[0]->field_iframe_de_la_carte_value;

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'carto',
			'#data' => $data,
		);
	}
}