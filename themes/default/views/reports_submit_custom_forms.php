<div id="custom_forms">
					
     <?php
                                
	foreach ($disp_custom_fields as $field_id => $field_property)
	{
		echo "<div class=\"report_row\">";
		echo "<h4>" . $field_property['field_name'] . "</h4>";
		if ($field_property['field_type'] == 1)
		{ // Text Field
			// Is this a date field?
			if ($field_property['field_isdate'] == 1)
			{
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
			else
			{
				echo form::input('custom_field['.$field_id.']', $form['custom_field'][$field_id],
				' id="custom_field_'.$field_id.'" class="text custom_text"');
			}
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			echo form::textarea('custom_field['.$field_id.']', $form['custom_field'][$field_id], ' class="custom_text" rows="3"');
		}
		echo "</div>";
	}
	?>
</div>

