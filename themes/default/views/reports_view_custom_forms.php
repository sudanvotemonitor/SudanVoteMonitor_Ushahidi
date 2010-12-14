<?php if(count($form_field_names) > 0) { ?>

<div class="report-custom-forms-text">
	<?php
	foreach ($form_field_names as $field_id => $field_property)
	{
		echo "<div class=\"report-custom-forms-row\">";
		if ($field_property['field_type'] == 1)
		{ // Text Field
			// Is this a date field?
			echo "<strong>" . $field_property['field_name'] . ": </strong>";
			if ($field_property['field_isdate'] == 1)
			{
				echo date('M d Y', strtotime(array_shift($disp_custom_fields)));		
			}
			else
			{
				echo array_shift($disp_custom_fields);
			}
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			echo "<h5>" . $field_property['field_name'] . "</h5>";
			echo (array_shift($disp_custom_fields));
		}
		echo "</div>";
	}
	?>

</div>

<?php } ?>
