<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Clickatell HTTP Post Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Clickatell Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
*/

class Clickatell_Controller extends Controller {
	
	private $request = array();
	
	public function __construct()
    {
        $this->request = ($_SERVER['REQUEST_METHOD'] == 'POST')
            ? $_POST
            : $_GET;
    }
	
	function index($key = NULL)
	{

		if (isset($this->request['from']))
		{
			$message_from = $this->request['from'];
			// Remove non-numeric characters from string
			$message_from = preg_replace("#[^0-9]#", "", $message_from);
		}
		
		if (isset($this->request['to']))
		{
			$message_to = $this->request['to'];
			// Remove non-numeric characters from string
			$message_to = preg_replace("#[^0-9]#", "", $message_to);
		}
		
		if (isset($this->request['text']))
		{
			# we need to grab the raw query string as was sent to the server, pre-decoding
			parse_str($_SERVER['QUERY_STRING'], $arr);

			if(isset($this->request['charset']))
			{
				$message_description  = ((mb_convert_encoding (urldecode($arr['text']),"UTF-8",$this->request['charset'])));
			}
			else 
			{
				$message_description = ((mb_convert_encoding (urldecode($arr['text']),"UTF-8", "auto")));
			}
		}
		
		if ( ! empty($message_from) AND ! empty($message_description))
		{
			// Is this a valid FrontlineSMS Key?
			$keycheck = ORM::factory('clickatell')
				->where('clickatell_key', $key)
				->find(1);

			if ($keycheck->loaded == TRUE)
			{
				sms::add($message_from, $message_description, $message_to);
			}
		}
	}
}
