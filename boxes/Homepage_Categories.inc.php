<?php
// /*

// +--------------------------------------------------------------------------

// |	homepage_categories.inc.php

// |   ========================================

// |	Homepage Categories * Written by Aare Laanesaar, Swansea Timber & Plywood

// +--------------------------------------------------------------------------

// */

if (!defined('CC_INI_SET')) die('Access Denied');

/* Import classes */
require_once('modules'.CC_DS.'3rdparty'.CC_DS.'Homepage_Categories'.CC_DS.'classes'.CC_DS.'Homepage_Categories.php');

if($categories->data) {

	$box_content = new XTemplate ('boxes'.CC_DS.'Homepage_Categories.tpl');

		$box_content->assign('HOMEPAGE_CATEGORIES_HEADER', '<p class="homepage_categories_header">Our most popular categories</p>');
		$box_content->parse('Homepage_Categories.homepage_categories_header');

		for($i=0; $i<=$categories->dataCount-1; $i++) {

		if($categories->cat_image[$i]) {
		
		/* Assign category data */
		$box_content->assign('CAT_NAME',  $categories->cat_name[$i]);
		$box_content->assign('CAT_LINK',  $categories->cat_link[$i]);
		$box_content->assign('CAT_IMAGE', $categories->cat_image[$i]);

		

		
		/* Assign child category data */
		/* This feature needs more development on the frontend */

		// if($categories->child_data_controller['name'][$i] !== 0) {

		// for($j=0; $j<=count($categories->child_data_controller['name'][$i])-1; $j++) {

		// $box_content->assign('CHILD_CAT_NAME', 	$categories->child_data_controller['name'][$i][$j]);
		// $box_content->assign('CHILD_CAT_IMAGE', $categories->child_data_controller['dynamic_image'][$i][$j]);
		// $box_content->assign('CHILD_CAT_LINK',  $categories->child_data_controller['link'][$i][$j]);

		// $box_content->parse('Homepage_Categories.category_loop.child_cats.child_cat_loop');

		// }

		// $box_content->parse('Homepage_Categories.category_loop.child_cats');

		// }
		/* This feature needs more development on the frontend */

		$box_content->parse('Homepage_Categories.category_loop');

		}

		}
			

		$box_content->parse('Homepage_Categories');
		$box_content = $box_content->text("Homepage_Categories");

	}


?>