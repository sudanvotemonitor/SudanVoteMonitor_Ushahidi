<div id="custom_forms">
					
     <?php
	foreach ($disp_custom_fields as $field_id => $field_property)
	{
		$isrequired = "";

		if($field_property['field_required'])
			$isrequired = "<font color=red>*</font>";

		echo "<div class=\"report_row\">";
		echo "<h4>" . $field_property['field_name'] . $isrequired . "</h4>";

		// Workaround for situtaions where admin can view, but doesn't have suff. perms to edit.
		if (isset($custom_field_mismatch))
		{
			if(isset($custom_field_mismatch[$field_id]))
			{
				if(isset($form['custom_field'][$field_id]))
					echo $form['custom_field'][$field_id];
				else
					echo "no data";
				echo "</div>";
				continue;
			}
		}

		if ($field_property['field_type'] == 1)
		{ // Text Field
			echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id], ' class="text custom_text"');
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
		{// Multiple-selector Fields
			$defaults = explode('::',$field_property['field_default']); 
			$default = 0;

			if(isset($defaults[1]))
				$default = $defaults[1];

			if (isset($form['custom_field'][$field_id]))
				if($form['custom_field'][$field_id] != '')
					$default = $form['custom_field'][$field_id];

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

						$html .= "<span style=\"margin-right: 15px\">";
						$html .= form::label('custom_field['.$field_id.']'," ".$option." ");
						$html .= form::radio('custom_field['.$field_id.']',$option,$set_default);
						$html .= "</span>";
					}
					break;
				case 6:
					$multi_defaults = explode(',',$default);
					$cnt = 0;
					$html .= "<table border=\"0\">";
					foreach($options as $option){
						if($cnt % 2 == 0)
							$html .= "<tr>";
						$html .= "<td>";
						$set_default = FALSE;	
						foreach($multi_defaults as $def){
							if($option == $def)
								$set_default = TRUE;	
						}
						$html .= "<span style=\"margin-right: 15px\">";
						$html .= form::checkbox("custom_field[".$field_id.'-'.$cnt.']',$option,$set_default);
						$html .= form::label("custom_field[".$field_id.']'," ".$option);
						$html .= "</span>";

						$html .= "</td>";
						if ($cnt % 2 == 1 || $cnt == count($options)-1)
							$html .= "</tr>";

						$cnt++;
					}
					// XXX Hack to deal with required checkboxes that are submitted with nothing checked
					$html .= "</table>";
					$html .= form::hidden("custom_field[".$field_id."-BLANKHACK]",'');
					break;
				case 7:
					$ddoptions = array();
					// Semi-hack to deal with dropdown boxes receiving a range like 0-100
					if(preg_match("/[0-9]+-[0-9]+/",$defaults[0]) && count($options == 1)){
						$dashsplit = explode('-',$defaults[0]);
						$start = $dashsplit[0];
						$end = $dashsplit[1];
						for($i = $start; $i <= $end; $i++)
							$ddoptions[$i] = $i;
					}
					else{
						foreach($options as $op)
							$ddoptions[$op] = $op;
					}

					$html .= form::dropdown("custom_field[".$field_id.']',$ddoptions,$default);
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
	
			$form_fields .= "	<div class=\"forms_fields_edit\">
			<a href=\"javascript:fieldAction('e','EDIT',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">EDIT</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('d','DELETE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">DELETE</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('mu','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE UP</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('md','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE DOWN</a>&nbsp;|&nbsp;
			". Kohana::lang('ui_admin.required').": ".$isrequired."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.reports_btn_submit').": ".$visibility_selection[$field_property['field_ispublic_submit']]."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.view').": ".$visibility_selection[$field_property['field_ispublic_visible']]."
			</div>";
			echo $form_fields;
		}
		
		echo "</div>";
	}
	?>
</div>

