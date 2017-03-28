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
		$data['test'] = "test";

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'gallery',
			'#data' => $data,
		);
	}
}