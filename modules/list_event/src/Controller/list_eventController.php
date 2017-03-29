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
		$data['test'] = "test";

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'list_event',
			'#data' => $data,
		);
	}
}