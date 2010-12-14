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
	public function get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false, $public_visible = 0, $public_submit = 0)
	{
		$fields_array = array();

		if (!$form_id)
			$form_id = 1;
	
		//added by george to only pull public forms if set in function call
		$public_state = array('field_ispublic_visible >='=>$public_visible, 'field_ispublic_submit >='=>$public_submit);
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
					'field_required' => $custom_formfield->field_required,
					'field_maxlength' => $custom_formfield->field_maxlength,
					'field_height' => $custom_formfield->field_height,
					'field_width' => $custom_formfield->field_width,
					'field_isdate' => $custom_formfield->field_isdate,
					'field_response' => ''
					);
			}
		}

		return $fields_array;
	}
	
	
	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 */
	function validate_custom_form_fields($custom_fields = array())
	{
		$custom_fields_error = "";

		foreach ($custom_fields as $field_id => $field_response)
		{
			// Get the parameters for this field
			$field_param = ORM::factory('form_field', $field_id);

			if ($field_param->loaded == true)
			{
				// Validate for required
				if ($field_param->field_required == 1 AND $field_response == "")
					return false;

				// Validate for date
				if ($field_param->field_isdate == 1 AND $field_response != "")
				{
					$myvalid = new Valid();
					return $myvalid->date_mmddyyyy($field_response);
				}
			}
		}

		return true;
	}


}
