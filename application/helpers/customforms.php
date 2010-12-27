<?php
/**
 * Custom Forms Helper
 * Functions to pull in the custom form fields and display them
 * 
 * @package    Custom Forms
 * @author     The Konpa Group - http://konpagroup.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class customforms_Core {

	/**
	 * Retrieve Custom Form Fields
	 * @param bool|int $incident_id The unique incident_id of the original report
	 * @param int $form_id The unique form_id. Uses default form (1), if none selected
	 * @param bool $field_names_only Whether or not to include just fields names, or field names + data
	 * @param bool $data_only Whether or not to include just data
	 * @param string $action If this is being used to grab fields for submit or view of data
	 */
	public function get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false, $action = "submit")
	{
		$fields_array = array();

		if (!$form_id)
			$form_id = 1;
		
		//NOTE will probably need to add a user_level variable for non-web based requests
		$user_level = customforms::get_user_max_auth();

		if ($action == "view")
			$public_state = array('field_ispublic_visible <='=>$user_level);
		else
			$public_state = array('field_ispublic_submit <='=>$user_level);

		$custom_form = ORM::factory('form', $form_id)->where($public_state)->orderby('field_position','asc');
		
		foreach ($custom_form->form_field as $custom_formfield)
		{
			if ($data_only)
			{
				// Return Data Only
				$fields_array[$custom_formfield->id] = '';

				foreach ($custom_formfield->form_response as $form_response)
				{
					if ($form_response->incident_id == $incident_id)
						$fields_array[$custom_formfield->id] = $form_response->form_response;
				}
			}
			else
			{
				// Return Field Structure
				$fields_array[$custom_formfield->id] = array(
					'field_id' => $custom_formfield->id,
					'field_name' => $custom_formfield->field_name,
					'field_type' => $custom_formfield->field_type,
					'field_default' => $custom_formfield->field_default,
					'field_required' => $custom_formfield->field_required,
					'field_maxlength' => $custom_formfield->field_maxlength,
					'field_height' => $custom_formfield->field_height,
					'field_width' => $custom_formfield->field_width,
					'field_isdate' => $custom_formfield->field_isdate,
					'field_ispublic_visible' => $custom_formfield->field_ispublic_visible,
					'field_ispublic_submit' => $custom_formfield->field_ispublic_submit,
					'field_response' => ''
					);
			}
		}

		return $fields_array;
	}


	/**
	 * Returns the user's maximum role id number
	 * @param array $user the current user object
	 */
	public function get_user_max_auth(){
        if(! isset($_SESSION['auth_user']))
			return 0;

		$user = new User_Model($_SESSION['auth_user']->id);
		
		if($user->loaded == true){
			$r = array();
			foreach($user->roles as $role){
				array_push($r,$role->id);
			}
			return max($r);
		}
		return 0;
	}
	
	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 */
	public function validate_custom_form_fields(&$post)
	{
		$errors = array();
		$custom_fields = array();

		/* XXX Checkboxes hackery 
			 Checkboxes are submitted in the post as custom_field[field_id-boxnum]
			 This foreach loop consolidates them into one variable separated by commas.
			 If no checkboxes are selected then the custom_field[] for that variable is not sent
			 To get around that the view sets a hidden custom_field[field_id-BLANKHACK] field that 
			 ensures the checkbox custom_field is there to be tested.
		*/
		foreach ($post->custom_field as $field_id => $field_response)
		{
			$split = explode("-", $field_id);
			if (isset($split[1]))
			{
				// The view sets a hidden field for blankhack
				if ($split[1] == 'BLANKHACK')
				{
					if(!isset($custom_fields[$split[0]]))
					{
						$custom_fields[$split[0]] = 'BLANKHACK';	
						continue;
					}
					else
						continue;
				}
				
				if (isset($custom_fields[$split[0]]))
					$custom_fields[$split[0]] .= ",$field_response";
				else
					$custom_fields[$split[0]] = $field_response;

			}
			else
				$custom_fields[$split[0]] = $field_response;
		}
	
		$post->custom_field = $custom_fields;

		foreach ($post->custom_field  as $field_id => $field_response)
		{
		
			$field_param = ORM::factory('form_field',$field_id);
			$custom_name = $field_param->field_name;

			// Validate that this custom field already exists
			if ( ! $field_param->loaded)
			{
				array_push($errors,"The $custom_name field does not exist");
				return $errors;
			}
			
			$max_auth = customforms::get_user_max_auth();
			if ($field_param->field_ispublic_submit > $max_auth)
			{
				array_push($errors, "The $custom_name field cannot be edited by your account");
				return $errors;
			}

			// Validate that the field is required
			if ( $field_param->field_required == 1 AND $field_response == "")
				array_push($errors,"The $custom_name field is required");

			// Validate for date
			if ($field_param->field_type == 3 AND $field_response != "")
			{
				$myvalid = new Valid();
				$myvalid->date_mmddyyyy($field_response);
				if (!$myvalid->date_mmddyyyy($field_response))
					array_push($errors,"The $custom_name field is not a valid date (MM/DD/YYYY)");
			}

			// Validate multi-value boxes only have acceptable values
			if ($field_param->field_type >= 5 && $field_param->field_type <=7)
			{
				$defaults = explode('::',$field_param->field_default);
				$options = explode(',',$defaults[0]);
				$responses = explode(',',$field_response);
				foreach($responses as $response)
					if( ! in_array($response, $options) && $response != 'BLANKHACK')
						array_push($errors,"The $custom_name field does not include $response as an option");
			}

			// Validate that a required checkbox is checked
			if ($field_param->field_type == 6 && $field_response == 'BLANKHACK' && $field_param->field_required == 1)
			{
				array_push($errors,"The $custom_name field is required");
			}


		}

		return $errors;
	}

    /**
    * Generate list of currently created Form Fields for the admin interface
    * @param int $form_id The id no. of the form
    */
    public function get_current_fields($form_id = 0)
    {  
		$form_fields = "<form action=\"\">";
		$form = array();
        $form['custom_field'] = customforms::get_custom_form_fields('',$form_id,true);
        $form['id'] = $form_id;
        $custom_forms = new View('reports_submit_custom_forms');
        $disp_custom_fields = customforms::get_custom_form_fields('',$form_id,false);
        $custom_forms->disp_custom_fields = $disp_custom_fields;
        $custom_forms->form = $form;
		$custom_forms->editor = true;
		$form_fields.= $custom_forms->render();
		$form_fields .= "</form>";
	
		return $form_fields;
	}

	/** 
	* Generates the html that's passed back in the json switch_Action form switcher
	* @param int $incident_id The Incident Id
	* @param int $form_id Form Id
	* @param int $public_visible If this form should be publicly visible
	* @param int $pubilc_submit If this form is allowed to be submitted by anyone on the internets.
	*/
	public function switcheroo($incident_id = '', $form_id = ''){
        $form_fields = '';

        $fields_array = customforms::get_custom_form_fields($incident_id, $form_id, true);

        $form = array();
        $form['custom_field'] = customforms::get_custom_form_fields($incident_id,$form_id,true);
        $form['id'] = $form_id;
        $custom_forms = new View('reports_submit_custom_forms');
        $disp_custom_fields = customforms::get_custom_form_fields($incident_id,$form_id,false);
        $custom_forms->disp_custom_fields = $disp_custom_fields;
        $custom_forms->form = $form;
        $form_fields.= $custom_forms->render();
		
		return $form_fields;	
	}
	
	/** 
	* Generates an array of fields that an admin can see but can't edit
	* @param int $form_id The form id
	**/
	public function get_edit_mismatch($form_id = 0)
	{
		$user_level = customforms::get_user_max_auth();
		$public_state = array('field_ispublic_submit >'=>$user_level, 'field_ispublic_visible <='=>$user_level);
		$custom_form = ORM::factory('form', $form_id)->where($public_state)->orderby('field_position','asc');
		$mismatches = array();	
		foreach ($custom_form->form_field as $custom_formfield)
		{
			$mismatches[$custom_formfield->id] = 1;
		}
		return $mismatches;
	}
}

