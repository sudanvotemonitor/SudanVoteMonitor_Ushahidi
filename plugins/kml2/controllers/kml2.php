<?php defined('SYSPATH') or die('No direct script access.');
/**
 * KML Controller
 * Generates KML with PlaceMarkers and Category Styles,
 * organized by categories and subcategories
 *
 * Version customized by Chris Roblee, robleec@gmail.com
 * -Based on David Kobian's initial kml plugin
 * -Chris's very first plugin (or meaningful PHP for that matter)!
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Feed Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 *
 */

class Kml2_Controller extends Controller
{
	public function index()
	{
		// How Many Items Should We Retrieve?
		if (isset($_GET['l']) AND !empty($_GET['l']))
		{
			$limit = (int) $_GET['l'];
		}
		else
		{
			$limit =intval(99999999999999999);
		}


		$cat_incidents = array();
		$categories = array();
		$cat_to_subcats = array();
		$subcat_to_incidents = array();

		// Grab all categories and add them to the list
		foreach(ORM::factory('category')
		->where('category_visible', '1')
		->find_all() as $cat) {
			// all categories
			array_push($categories, $cat);

			// top-level category
			if($cat->parent_id == 0) {
				// first time, so initialize mapping
				if(!isset($cat_to_subcats[$cat->id])) {
					$cat_to_subcats[$cat->id] = array();
				}
			}
			else {
				// first time, so initialize mapping
				if(!isset($cat_to_subcats[$cat->parent_id])) {
					$cat_to_subcats[$cat->parent_id] = array();
				}
				// this is a sub-category, map it to its parent category
				array_push($cat_to_subcats[$cat->parent_id], $cat);
			}
		}

		// grab all incidents within limit
		$incidents = ORM::factory('incident')
		->where('incident_active', '1')
		->orderby('incident_date', 'desc')
		->limit($limit)
		->find_all();
			
		//		echo "LIMIT " . $limit;

		// Get the "first" category of each incident, and save both in a map of arrays
		//  category id -> [incidents]
		foreach($incidents as $incident) {

			// for each category and subcategory that this incident belongs to (they are all in one array):
			foreach($incident->category as $cat) {
				// if it is a sub-category
				if($cat->parent_id != 0) {
					//					echo("sub cat id:" . $cat->id . "\n");
					// first time with this sub-category
					if(!isset($subcat_to_incidents[$cat->id])) {
						$subcat_to_incidents[$cat->id] = array();
					}
					array_push($subcat_to_incidents[$cat->id], $incident);
				}
				else if(count($incident->category) == 1){
					// this is a main category
					// first time with this category
					if(!isset($cat_incidents[$cat->id])) {
						$cat_incidents[$cat->id] = array();
					}
					array_push($cat_incidents[$cat->id],$incident);
				}
			}
		}

		$happy_date = date("Y-m-d.H.i.s", time());

		// TODO: extract the sitename - there must be some cooler way to do this
		// e.g., "http://dev-haiti.stayready.net/"
		$host =  parse_url(url::site(), PHP_URL_HOST);
		header("Content-Type: application/vnd.google-earth.kml+xml");
		header("Content-Type: application/vnd.google-earth.kmz kmz");
		header("Content-Disposition: attachment; filename=". $host . ".". $happy_date.".kmz");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");

		$view = new View("kml2");
		$view->kml2_name = htmlspecialchars(Kohana::config('settings.site_name'));
		$view->cat_to_subcats = $cat_to_subcats; //$incidents;
		$view->subcat_to_incidents = $subcat_to_incidents;
		$view->cat_incidents = $cat_incidents;
		$view->categories = $categories;
		$view->render(TRUE);
	}
}
