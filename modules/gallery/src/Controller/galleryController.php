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
		* Get pictures's gallerie
		*/
		$query = db_query("
			SELECT f.uri, g.field_gallery_alt,
			g.field_gallery_title
			from file_managed f
			join node__field_gallery g
			on f.fid = g.field_gallery_target_id
		");

		$pictures = $query->fetchAll();

		foreach ($pictures as $key => $picture) {

			$picture->uri = entity_load('image_style', '500_par_350')->buildUrl($picture->uri);

		}

		// add to global var data
		$data['pictures'] = $pictures;

		/*
		* Return all data
		*/
		return array(
			'#theme' => 'gallery',
			'#data' => $data,
		);
	}
}