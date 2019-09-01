<?php
/**
 * Builds tables for the site, in form of html table in single string and 
 * a PHP array.
 *
 * @param connection Connection to db with logs data.
 * @return tableName String with name of the table in the db.
 */
function makeTable($connection, $tableName) {
	// If connection failed echo errror.
	if ($connection->connect_errno != 0) {
			echo "Error: ".$connection->connect_errno;
	} else {
		try {
			// Make query to get while log table.
			$query = "SELECT * FROM " . $tableName;
			$html_table_log =
				"<table class = 'log_table' id = '$tableName" . "_table'>";
			$html_table_log .= "<thead>";
			$result = $connection->query($query);
			// We want the first row for col names.
			$row = $result->fetch_assoc();
			$html_table_log .= " <tr>";
			foreach ($row as $field => $value){
				$html_table_log .= " <th>$field</th>";
			}
			$html_table_log .= " </tr>";
			$html_table_log .= "</thead><tbody>";
			
			// Print actual data as html table inside of a string
			foreach($result as $row){
				// Table header.
				$html_table_log .= " <tr>";
				$array_table_log[] = $row;
				// Build table body in for loop.
				foreach ($row as $name=>$value){
					$html_table_log .= " <td>$value</td>";
				}
				$html_table_log .= " </tr>";
			}
			// End table.
			$html_table_log .= "</tbody></table>";
			return array($html_table_log, $array_table_log);
		} catch(PDOException $e) {
		 echo 'ERROR: ' . $e->getMessage();
		}
	}
}
?>

