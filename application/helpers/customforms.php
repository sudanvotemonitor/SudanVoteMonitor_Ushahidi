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
	
		//added by george to only pull public forma if set in function call
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
					'field_default' => $custom_formfield->field_default,
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

    /**
    * Generate list of currently created Form Fields
    * @param int $form_id The id no. of the form
    */
    public function get_current_fields($form_id = 0)
    {  
		$fields = ORM::factory('form_field')
			->where('form_id', $form_id)
			->orderby('field_position', 'asc')
			->orderby('id', 'asc')
			->find_all();

		$form_fields = "<form action=\"\">";
		foreach ($fields as $field)
		{
			$field_id = $field->id;
			$field_name = $field->field_name;
			$field_default = $field->field_default;
			$field_required = $field->field_required;
			$field_width = $field->field_width;
			$field_height = $field->field_height;
			$field_maxlength = $field->field_maxlength;
			$field_position = $field->field_position;
			$field_type = $field->field_type;
			$field_isdate = $field->field_isdate;
			$field_ispublic_visible = $field->field_ispublic_visible;
			$field_ispublic_submit = $field->field_ispublic_submit;

			$form_fields .= "<div class=\"forms_fields_item\">";
			$form_fields .= "	<strong>".$field_name.":</strong><br />";
			if ($field_type == 1)
			{
				$form_fields .= form::input("custom_".$field_id, '', '');
			}
			elseif ($field_type == 2)
			{
				$form_fields .= form::textarea("custom_".$field_id, '');
			}
			elseif ($field_type == 3)
			{
            	$form_fields .= "<script type=\"text/javascript\">
                	$(document).ready(function() {
                	$(\"#custom_".$field_id."\").datepicker({ 
                	showOn: \"both\", 
       	        	 buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
       	         	buttonImageOnly: true 
        	        });
    	            });
		            </script>";
				$form_fields .= form::input("custom_".$field_id, '', '');
			}
			elseif ($field_type >= 5 && $field_type <= 7)
			{
				$defaults = explode('::',$field_default);
				$default = 0;
				if(isset($defaults[1])){
					$default = $defaults[1];
			}
				$options = explode(',',$defaults[0]);
				
				switch ($field_type){
					case 5:
						foreach($options as $option){
							if($option == $default){
								$set_default = TRUE;	
							}else{
								$set_default = FALSE;	
							}
							$form_fields .= form::label("custom_".$field_id," ".$option." ");
							$form_fields .= form::radio("custom_".$field_id,$option,$set_default);
						}
						break;
					case 6:
							$multi_defaults = explode(',',$default);
								foreach($options as $option){
							$set_default = FALSE;	
							foreach($multi_defaults as $def){
								if($option == $def)
									$set_default = TRUE;	
							}
							$form_fields .= form::label("custom_".$field_id," ".$option." ");
							$form_fields .= form::checkbox("custom_".$field_id,$option,$set_default);
						}
						break;
					case 7:
						$form_fields .= form::dropdown("custom_".$field_id,$options,$default);
						break;

				}
			}
			/*if ($field_isdate == 1) 
			{
				$form_fields .= "&nbsp;<a href=\"#\"><img src = \"".url::base()."media/img/icon-calendar.gif\"  align=\"middle\" border=\"0\"></a>";
			}*/
				
			$visibility = Kohana::lang('ui_admin.visible_admin');
			if($field_ispublic_visible)
				$visibility = Kohana::lang('ui_admin.visible_public');

			$submitability = Kohana::lang('ui_admin.visible_admin');
			if($field_ispublic_submit)
				$submitability = Kohana::lang('ui_admin.visible_public');

			$isrequired = Kohana::lang('ui_admin.no');
			if($field_required)
				$isrequired = Kohana::lang('ui_admin.yes');

			$form_fields .= "	<div class=\"forms_fields_edit\">
			<a href=\"javascript:fieldAction('e','EDIT',".$field_id.",".$form_id.",".$field_type.");\">EDIT</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('d','DELETE',".$field_id.",".$form_id.",".$field_type.");\">DELETE</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('mu','MOVE',".$field_id.",".$form_id.",".$field_type.");\">MOVE UP</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('md','MOVE',".$field_id.",".$form_id.",".$field_type.");\">MOVE DOWN</a>&nbsp;|&nbsp;
			". Kohana::lang('ui_admin.required').": ".$isrequired."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.reports_btn_submit').": ".$submitability."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.view').": ".$visibility."
			</div>";
			$form_fields .= "</div>";
		}
		$form_fields .= "</form>";
	
		return $form_fields;
	}
}

