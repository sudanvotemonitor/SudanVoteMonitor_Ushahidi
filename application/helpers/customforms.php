<?php
/**
 * Custom Forms Helper
 * Functions to pull in the custom form fields
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
	 * @param bool $public_visible Whether or not this is being viewed publicly
	 * @param bool $public_submit Whether or not this is being submitted publicly
	 */
	public function get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false)
	{
		$fields_array = array();

		if (!$form_id)
			$form_id = 1;

		$user_level = customforms::get_user_max_auth();
		$public_state = array('field_ispublic_visible <='=>$user_level, 'field_ispublic_submit <='=>$user_level);
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
				array_push($r,$role);
			}
			return max($r);
		}
		return 0;
	}
	
	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 */
	public function validate_custom_form_fields(&$post, $user = FALSE)
	{

		foreach ($post->custom_field as $field_id => $field_response)
		{
		
			$field_param = ORM::factory('form_field',$field_id)->where('form_id','0')->orwhere('form_id',$post->form_id)->find();

			// Validate that this custom field already exists
			if ( ! $field_param->loaded)
			{
				$post->add_error('custom_field','default');
				return;
			}

			if ($field_param->field_ispublic_submit < customforms::get_user_max_auth($user))
			{
				$post->add_error('custom_field','default');
				return;
			}

			// Validate that the field is required
			if ( $field_param->field_required)
				$post->add_rules('custom_field','required');

			// check if it matches a specific type 
			

			// Get the parameters for this field
			//$field_param = ORM::factory('form_field', $field_id)->where('form_id','0')->where('form_id',$post->form_id);
			//$field_param = ORM::factory('form_field', $field_id)->orwhere(array('form_id' => 0, 'form_id' => $post->form_id));
/*
			if ($field_param->loaded == true)
			{
				// Validate that this form field can be submitted by this person
				//if ($field_param->field_ispublic_submit <= $public_submit)
				//	unset($post->custom_field[$field_id]);

				if ($field_param->form_id != 0 && $field_param->form_id != $post->form_id)
					$post->add_rules('custom_field','default');

				// Validate for required
				if ($field_param->field_required == 1 AND $field_response == "")
					$post->add_error('custom_field','required');

				// Validate for date
				if ($field_param->field_type == 3 AND $field_response != "")
				{
					$myvalid = new Valid();
					$post->add_error('custom_field','');
					return $myvalid->date_mmddyyyy($field_response);
				}
			}else{
echo "something went terribly wrong\n";
exit;
			}
			*/
		}

		return true;
	}

    /**
    * Generate list of currently created Form Fields
    * @param int $form_id The id no. of the form
    */
    public function get_current_fields($form_id = 0, $user = FALSE)
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
}

