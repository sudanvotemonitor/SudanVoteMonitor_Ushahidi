<?php



function create_placemark($incident, $category)
{
	$ret = "\n<Placemark id = \"" . $incident->id ."\">\n";
	$ret .= "<styleUrl>#category_" . $category . "</styleUrl>\n";
	$ret .=	"<visibility>1</visibility>\n";
	$ret .= "<name>" . htmlspecialchars($incident->incident_title) ."</name>\n";
	$ret .= "<description>\n";
	$ret .=  htmlspecialchars(text::limit_words($incident->incident_description, 50, "...")) . "\n";
	$ret .=	"<BR /><a href=\"".url::base().'reports/view/'.$incident->id."\">More...</a>\n";
	$ret .= "</description> \n";
	$ret .= "<Point> \n";
	$ret .= "	<coordinates>" . $incident->location->longitude . ",". $incident->location->latitude ."</coordinates>\n";
	$ret .= "</Point>\n";
	$ret .= "</Placemark>\n";
	return $ret;
}

function create_kmz($kml){

	$zip = new ZipArchive();

	// Use the pre-configured upload directory
	$directory = Kohana::config('upload.directory', TRUE);
	$file = "$directory/latest.kmz";

	if ($zip->open("$file", ZIPARCHIVE::CREATE)!==TRUE) {
		echo("cannot open <". $file .">\n");
	}

	$zip->addFile("plugins/kml2/views/circle_border.png", "files/circle_border.png");
	$zip->addFromString("latest.kml", $kml);
	$zip->close();
	return $zip;
}

// TODO: How to access variables passed from controller in functions?
function genKML($name, $categories, $cat_to_subcats, $subcat_to_incidents, $cat_incidents) {
	// KML header
	$kml = "<?xml version='1.0' encoding='UTF-8'?>" . PHP_EOL
	. "<kml xmlns='http://www.opengis.net/kml/2.2'>" . PHP_EOL
	. "  <Document>" . PHP_EOL;

	$kml.= "	<name>" . $name . "</name>". PHP_EOL;

	// make a mapping of category_id->category and generate styles!
	$catid_to_cat = array();

	$kmlStyles = "";
	foreach ($categories as $category) {
		$catid_to_cat[$category->id] = $category;
		$kmlStyles .= "<Style id='category_$category->id'>" . PHP_EOL
		. "  <IconStyle>" . PHP_EOL
		. "  	<color>FF$category->category_color</color>" . PHP_EOL
		. "  	<scale>0.8</scale>" . PHP_EOL
		. "		<Icon>" . PHP_EOL
		//		. " 			<href>" . url::base() ."plugins/kml2/views/circle_border.png</href>" . PHP_EOL
		. " 			<href>files/circle_border.png</href>" . PHP_EOL
		. "		</Icon>" . PHP_EOL
		. " 	</IconStyle> " . PHP_EOL
		. "</Style>" . PHP_EOL;

	}

$kmlRest = "";
	// for every category_id->[incidents] entry, generate one <Folder> per category, one <Folder>
	// per sub-category, and then one <Placemark> per incident
	// by default, all folders are closed, and all placemarks not visible
	foreach ($cat_to_subcats as $cat_id => $subcats) {


		$kmlRest .= "<Folder id ='$cat_id'>" . PHP_EOL
		. "  <visibility>1</visibility>" . PHP_EOL
		. "  <open>0</open>"  . PHP_EOL;

		$cat = $catid_to_cat[$cat_id];
		$kmlRest .= "<name>$cat->category_title</name>" .  PHP_EOL
		. "<description>$cat->category_description</description>";

		// for every subcategory, create a sub-folder and add any incidents to it
		foreach($subcats as $subcat) {
			$kmlRest .= "<Folder id = '$subcat->id'>" . PHP_EOL
			.  "	<visibility>1</visibility>" . PHP_EOL
			.  "	<open>0</open>" . PHP_EOL
			.  "	<name>$subcat->category_title</name>" . PHP_EOL
			.  "	<description>$subcat->category_description</description>" . PHP_EOL;

			if(isset($subcat_to_incidents[$subcat->id])) {
				// for each every incident in this subcategory, create a thingy
				foreach($subcat_to_incidents[$subcat->id] as $incident) {
					$kmlRest .= (create_placemark($incident, $subcat->id));
				}
			}
			$kmlRest .= "</Folder>" . PHP_EOL;
		}



		// for every incident in this category and NOT in a sub-category, add it
		if(isset($cat_incidents[$cat->id])) {
			foreach ($cat_incidents[$cat_id] as $incident){
				$kmlRest .= create_placemark($incident, $cat->id);
			}
		}
		$kmlRest .= "</Folder>" . PHP_EOL;
	}

	$kml .= $kmlStyles . $kmlRest;
	$kml .= "</Document>" . PHP_EOL
	.  "</kml>". PHP_EOL;
	return $kml;
}

$kml = genKML($kml2_name, $categories, $cat_to_subcats,$subcat_to_incidents, $cat_incidents);
$kmz = create_kmz($kml);
readfile(Kohana::config('upload.directory', TRUE) . "/latest.kmz");
?>




