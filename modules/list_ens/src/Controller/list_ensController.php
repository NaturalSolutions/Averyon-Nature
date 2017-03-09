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

		//Get image on top
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

		return array(
			'#theme' => 'list_ens',
			'#data' => $data,
		);

	}
}