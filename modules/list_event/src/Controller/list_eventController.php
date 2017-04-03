<?php

/**
 * @file
 * Contains \Drupal\list_event\Controller\list_eventController.
 */

namespace Drupal\list_event\Controller;

use Drupal\Core\Controller\ControllerBase;

class list_eventController extends ControllerBase {

	public function content() {

		$data = [];

		/*
		* Get Title
		*/
		$query = db_query("
			SELECT d.title FROM aveyron.node_field_data d where d.nid = 58
		");

		$title = $query->fetchAll();
		$data['title'] = $title[0]->title;

		/*
		* FOLD 1 - Get image on top
		*/
		$query = db_query("
			SELECT f.uri, s.field_image_on_top_alt,
			s.field_image_on_top_title
			from file_managed f
			join node__field_image_on_top s
			on f.fid = s.field_image_on_top_target_id
			join node_field_data d
			on d.nid = s.entity_id
			where d.nid = 58
		");

		$imageOnTop = $query->fetchAll();

		//$ens->uri = entity_load('image_style', '470_par_750')->buildUrl($ens->uri);
		$imageOnTop[0]->uri = file_create_url($imageOnTop[0]->uri);
		$data['imageOnTop'] = $imageOnTop[0];

		/*
		* Get Event datas
		*/
		$query = db_query("
			SELECT d.title, d.nid, m.uri, l.field_place_value as lieu, e.field_moment_evt_value as date
			FROM aveyron.node_field_data d
			join node__field_poster p
			on p.entity_id = d.nid
			join file_managed m
			on m.fid = p.field_poster_target_id
			join node__field_place l
			on l.entity_id = d.nid
			join node__field_moment_evt e
			on e.entity_id = d.nid
			where d.type = 'event'
			order by d.created ASC
		");

		$events = $query->fetchAll();

		foreach ($events as $key => $event) {

			//Add alias path
			$path_alias = \Drupal::service('path.alias_manager')->getAliasByPath("/node/".$event->nid);
			$path_alias = ltrim($path_alias, '/');
			$event->url_alias = $path_alias;

			// convert style
			$event->uri = entity_load('image_style', '500_par_350')->buildUrl($event->uri);

			// remove useless properties
			unset($event->nid);

		}

		$data['events'] = $events;

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'list_event',
			'#data' => $data,
		);
	}
}