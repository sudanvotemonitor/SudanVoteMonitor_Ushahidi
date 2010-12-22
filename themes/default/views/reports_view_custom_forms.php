<?php if(count($form_field_names) > 0) { ?>

<div class="report-custom-forms-text">
	<?php
	foreach ($form_field_names as $field_id => $field_property)
	{
		$value = array_shift($disp_custom_fields);
		if($value == "")
			continue;

		echo "<div class=\"report-custom-forms-row\">";
		if ($field_property['field_type'] == 1 || $field_property['field_type'] > 3)
		{ // Text Field
			// Is this a date field?
			echo "<strong>" . $field_property['field_name'] . ": </strong>";
			echo $value;
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			echo $value;
		}
		elseif ($field_property['field_type'] == 3)
		{
			echo "<strong>" . $field_property['field_name'] . ": </strong>";
			echo date('M d Y', strtotime($value));		
		}
		echo "</div>";
	}
	?>

</div>

<?php } ?>
