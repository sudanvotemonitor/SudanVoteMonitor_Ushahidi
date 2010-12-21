<div id="custom_forms">
					
     <?php
                                
	foreach ($disp_custom_fields as $field_id => $field_property)
	{
		echo "<div class=\"report_row\">";
		echo "<h4>" . $field_property['field_name'] . "</h4>";
		if ($field_property['field_type'] == 1)
		{ // Text Field
			echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id], ' id="custom_field_'.$field_id.'" class="text custom_text"');
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			echo form::textarea('custom_field['.$field_id.']', $form['custom_field'][$field_id], ' class="textarea custom_text" rows="3"');
		}
		elseif ($field_property['field_type'] == 3)
		{ // Date Field
			echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id],
			' id="custom_field_'.$field_id.'" class="text"');
			echo "<script type=\"text/javascript\">
				$(document).ready(function() {
				$(\"#custom_field_".$field_id."\").datepicker({ 
				showOn: \"both\", 
				buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
				buttonImageOnly: true 
				});
				});
			</script>";
		}
		elseif ($field_property['field_type'] >=5 && $field_property['field_type'] <=7)
		{
			$defaults = explode('::',$field_property['field_default']); 
			$default = 0;
			if(isset($defaults[1])){
					$default = $defaults[1];
				}
			$options = explode(',',$defaults[0]);
			$html ='';	
			switch ($field_property['field_type']){
				case 5:
					foreach($options as $option){
						if($option == $default){
							$set_default = TRUE;	
						}else{
							$set_default = FALSE;	
						}
						$html .= form::label('custom_field['.$field_id.']'," ".$option." ");
						$html .= form::radio('custom_field['.$field_id.']',$option,$set_default);
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
						$html .= form::label("custom_field[".$field_id.']'," ".$option." ");
						$html .= form::checkbox("custom_field[".$field_id.']',$option,$set_default);
					}
					break;
				case 7:
					$html .= form::dropdown("custom_field[".$field_id.']',$options,$default);
					break;

			}
			echo $html;
		}	
		if (isset($editor)){
			$form_fields = '';
        	$visibility_selection = array('0' => Kohana::lang('ui_admin.anyone_role'));
			$roles = ORM::factory('role')->find_all();
			foreach($roles as $role){
		    	$langname = 'ui_admin.'.$role->name.'_role';
				$visibility_selection[$role->id] = Kohana::lang($langname);
			}
			
			$isrequired = Kohana::lang('ui_admin.no');
			if($field_property['field_required'])
				$isrequired = Kohana::lang('ui_admin.yes');
		/*	
			$visibility = Kohana::lang('ui_admin.visible_admin');
			if($field_property['field_ispublic_visible'])
				$visibility = Kohana::lang('ui_admin.visible_public');

			$submitability = Kohana::lang('ui_admin.visible_admin');
			if($field_property['field_ispublic_submit'])
				$submitability = Kohana::lang('ui_admin.visible_public');

		*/
			$form_fields .= "	<div class=\"forms_fields_edit\">
			<a href=\"javascript:fieldAction('e','EDIT',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">EDIT</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('d','DELETE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">DELETE</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('mu','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE UP</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('md','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE DOWN</a>&nbsp;|&nbsp;
			". Kohana::lang('ui_admin.required').": ".$isrequired."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.reports_btn_submit').": ".$visibility_selection[$field_property['field_ispublic_visible']]."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.view').": ".$visibility_selection[$field_property['field_ispublic_submit']]."
			</div>";
			echo $form_fields;
		}
		
		echo "</div>";
	}
	?>
</div>

