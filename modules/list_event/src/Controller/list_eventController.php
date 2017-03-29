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
		* Get Event datas
		*/
		$query = db_query("
			SELECT d.title, m.uri, l.field_place_value
			FROM aveyron.node_field_data d
			join node__field_poster p
			on p.entity_id = d.nid
			join file_managed m
			on m.fid = p.field_poster_target_id
			join node__field_place l
			on l.entity_id = d.nid
			where d.type = 'event'
			order by d.created DESC
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