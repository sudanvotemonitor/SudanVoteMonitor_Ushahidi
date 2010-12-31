<?php if(count($form_field_names) > 0) { ?>

<div class="report-custom-forms-text">
	<?php
	echo "<table >";
	foreach ($form_field_names as $field_id => $field_property)
	{
		echo "<tr>";
		$value = array_shift($disp_custom_fields);
		if($value == "")
			continue;

		if ($field_property['field_type'] == 1 || $field_property['field_type'] > 3)
		{ // Text Field
			// Is this a date field?
			echo "<td style=\"padding:5px;width:300px;border-bottom: 1px dotted #C0C2B8;\"><strong>" . $field_property['field_name'] . ": </strong></td>";
			echo "<td style=\"border-bottom: 1px dotted #C0C2B8;\">$value</td>";
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			echo "<td style=\"padding:5px;border-bottom: 1px dotted #C0C2B8;\"><strong>" . $field_property['field_name'] . ": </strong></td>";
			echo "<td style=\"border-bottom: 1px dotted #C0C2B8;\">$value</tr>";
		}
		elseif ($field_property['field_type'] == 3)
		{
			echo "<td style=\"padding:5px;border-bottom: 1px dotted #C0C2B8;\"><strong>" . $field_property['field_name'] . ": </strong></td>";
			echo "<td style=\"border-bottom: 1px dotted #C0C2B8;\">" . date('M d Y', strtotime($value)) . "</td>";		
		}
		echo "</div>";
		echo "</tr>";
	}
	echo "</table>";
	?>

</div>

<?php } ?>
