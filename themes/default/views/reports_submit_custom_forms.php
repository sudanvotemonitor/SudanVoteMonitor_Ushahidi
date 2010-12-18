<div id="custom_forms">
					
     <?php
                                
	foreach ($disp_custom_fields as $field_id => $field_property)
	{
		echo "<div class=\"report_row\">";
		echo "<h4>" . $field_property['field_name'] . "</h4>";
		if ($field_property['field_type'] == 1)
		{ // Text Field
			echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id],
			' id="custom_field_'.$field_id.'" class="text custom_text"');
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
		echo "</div>";
	}
	?>
</div>

