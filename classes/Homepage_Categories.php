<?php
/*

* The purpose of this module is to support Homepage_Categories.inc.php controller.
* Written by Aare Laanesaar, Swansea Timber & Plywood

*/

class Homepage_Categories {

	public $image_full_path;

	public $data;
	public $dataCount;

	public $cat_link 		  = array();
	public $cat_image 		  = array();
	public $cat_name 		  = array();

	public $child_cat_data 			= array();
	// public $child_cat_images 		= array();
	// public $child_cat_links 		= array();
	// public $child_data_controller 	= array();

	public function __construct() {

	$this->get_data();
		
	if($this->data) {

	$this->setup();

	}

	}

	public function setup() {
		
	$this->set_image_full_path('images/uploads/thumbs');
	
	$this->set_data_count();
	// $this->set_child_data();
	// $this->set_child_cat_images();
	// $this->set_child_cat_links();
	// $this->create_child_data_controller();
	$this->set_data_properties();
	$this->generate_links();

	// $this->get_fathers();


	}

	public function get_data() {

	global $db;
	
	$results = $db->select("SELECT c.cat_id, c.cat_father_id, LOWER(c.cat_name) as name, c.cat_image FROM CubeCart_category as c WHERE home_page = '1' group by c.cat_name order by c.cat_name");

	$this->set_data($results);

	}

	public function set_data( $data ) {
		
	$this->data = $data;

	}

	public function set_data_count() {
		
	$this->dataCount = count($this->data);

	}


	public function set_data_properties() {
		
	$this->properties_by_data('cat_name', 'cat_image', $this->data);

	}


	public function properties_by_data($property_name, $property_image, $input_data) {

	foreach($input_data as $data) {

			array_push($this->$property_name, ucwords($data['name']));

			if(isset($data['cat_id'])) {

			$this->set_image($property_image, $data['cat_image'], $data['cat_id'], $data['cat_father_id']);

			}

		}

	}


	public function replace_symbols( $value , $replace_with ) {

	$to_replace = array( '.', '-', '/' );

	$value = str_replace( $to_replace, $replace_with, $value );
	
	return $value;
	
	}



	/* CATEGORY IMAGES */

	public function set_image($property_image, $image_name, $cat_id, $cat_father_id) {

	if($image_name) {

	$image = $this->image_full_path . "thumb_" . $this->format_image_name($image_name) . ".jpg";

	array_push($this->$property_image, $image);

	}

	else {

	if($this->get_child_cat_by_id( $cat_id )) {
		
	$child_cat = $this->get_child_cat_by_id( $cat_id );

	if($child_cat[0]['cat_id']) {

	$count = count($child_cat)-1;

	$random_index = rand(0, $count);

	$image = $this->image_by_cat_id($child_cat[$random_index]['cat_id']);

	}

	}

	else {

	$image =  $this->image_by_cat_id($cat_id);

	}

	array_push($this->$property_image, $image);

	}

	}

	public function format_image_name( $name ) {
		
	$name = str_replace('productImages/', 'thumb_', $name);

	$name = $this->replace_symbols($name, '_');

	$name = str_replace('_jpg', '.jpg', $name);

	return $name;

	}

	public function set_image_full_path( $relative_path ) {

	global $glob;

	$this->image_full_path = $glob['storeURL'] . "/". $relative_path . "/";

	}

	public function image_by_cat_id($cat_id) {

	global $db;

	$image = $db->select("SELECT image FROM CubeCart_inventory WHERE cat_id = $cat_id ORDER BY RAND() LIMIT 1");

	if($image[0]['image']) {

	$image_path = $this->image_full_path . $this->format_image_name($image[0]['image']);

	}

	else {
		
	$image_path = false;

	}

	return $image_path;

	}

	/* CATEGORY IMAGES */



	/* LINKS */

	public function generate_links() {

		foreach($this->data as $category_data) {

				if($category_data['cat_father_id'] == 0) {
					
				$link = $category_data['name'];

				}

				else {

						$father = $this->cat_father_by_id($category_data['cat_father_id']);

						if($father['cat_father_id'] == 0) {

						$link = $father['name']."/".$category_data['name'];

						}

						else {

						$grand_father = $this->cat_father_by_id($father['cat_father_id']);
							
						$link = $grand_father['name'] . "/" . $father['name'] . "/" . $category_data['name'];

						}

					}

			$link .= "/cat_" . $category_data['cat_id'] . ".html";

			$this->cat_link[] = $this->validate_link($link);

		}

	}

	public function validate_link($link) {
		
	$link = preg_replace('#\s#', '-', $link);

	return $link;

	}

	/* LINKS */


	public function cat_father_by_id($cat_father_id) {

	global $db;

	$father = $db->select("SELECT LOWER(cat_name) as name, cat_id, cat_father_id FROM CubeCart_category WHERE cat_id = $cat_father_id");

	return $father[0];

	}

	/* CHILD CATEGORIES */

	public function get_child_cat_by_id($cat_id) {

	global $db;
		
	$child = $db->select("SELECT LOWER(cat_name) as name, cat_id, cat_father_id, cat_image FROM CubeCart_category WHERE cat_father_id = $cat_id");

	return $child;

	}

	public function set_child_data() {
		
	for($i=0; $i<=$this->dataCount-1; $i++) {
		
	$childCat[]	   = $this->get_child_cat_by_id($this->data[$i]['cat_id']);

	}

	$this->child_cat_data = $childCat;

	}

	public function create_child_data_controller() {
		
	for($i=0; $i<=count($this->child_cat_data)-1; $i++) {

	if(is_array($this->child_cat_data[$i])) {

	for($j=0; $j<=count($this->child_cat_data[$i])-1; $j++) {

	$child_data_wrapper['name'][$i][] 			= $this->child_cat_data[$i][$j]['name'];

	$child_data_wrapper['cat_id'][$i][] 		= $this->child_cat_data[$i][$j]['cat_id'];

	$child_data_wrapper['cat_father_id'][$i][] 	= $this->child_cat_data[$i][$j]['cat_father_id'];

	$child_data_wrapper['custom_image'][$i][] 	= $this->child_cat_data[$i][$j]['cat_image'];

	}

	}

	else {

	$child_data_wrapper['name'][$i] 		  = 0;

	$child_data_wrapper['cat_id'][$i] 		  = 0;

	$child_data_wrapper['cat_father_id'][$i]  = 0;

	$child_data_wrapper['custom_image'][$i]   = 0;

	}

	}

	foreach($this->child_cat_images as $index => $image) {

	$child_data_wrapper['dynamic_image'][] = $image;

	}

	// foreach($this->child_cat_links as $index => $link) {

	// $child_data_wrapper['link'][] = $link;

	// }

	$this->child_data_controller = $child_data_wrapper;

	}

	public function set_child_cat_links() {

	for($i=0; $i<=count($this->child_cat_data)-1; $i++) {

	if(is_array($this->child_cat_data[$i])) {

	for($j=0; $j<=count($this->child_cat_data[$i])-1; $j++) {

	$father = $this->cat_father_by_id($this->child_cat_data[$i][$j]['cat_father_id']);

	$grand_father = $this->cat_father_by_id($father['cat_father_id']);

	$links[$i][] = $grand_father['name'] . "/" . $father['name'] . "/" . $this->child_cat_data[$i][$j]['name'] . "/cat_" . $this->child_cat_data[$i][$j]['cat_id'] . ".html";;

	}

	}

	else {
		
	$links[] = 0;

	}

	}

	$this->child_cat_links = $links;

	}

	public function set_child_cat_images() {

	global $glob;
		
	for($i=0; $i<=count($this->child_cat_data)-1; $i++) {
		
	if(is_array($this->child_cat_data[$i])) {
		
	foreach($this->child_cat_data[$i] as $key => $value) {
		
	$images[] = $glob['storeURL'] . "/images/uploads/thumbs/" . $this->image_by_cat_id($value['cat_id']);

	$imgs[$i] = $images;

	}

	}

	else {
		
	$imgs[] = 0;

	}

	unset($images);

	}

	$this->child_cat_images = $imgs;

	}

	/* CHILD CATEGORIES */

}

$categories = new Homepage_Categories();

?>