<?php
function makeTable($connection, $tableName) {
	if ($connection->connect_errno != 0) {
			echo "Error: ".$connection->connect_errno;
	} else {
		try {
			$query = "SELECT * FROM " . $tableName;
			$html_table_log = "<table>";
			$result = $connection->query($query);
			// We want the first row for col names
			$row = $result->fetch_assoc();
			$html_table_log .= " <tr>";
			foreach ($row as $field => $value){
				$html_table_log .= " <th>$field</th>";
			}
			$html_table_log .= " </tr>";

			// Print actual data
			foreach($result as $row){
				$html_table_log .= " <tr>";
				$array_table_log[] = $row;
				foreach ($row as $name=>$value){
					$html_table_log .= " <td>$value</td>";
				}
				$html_table_log .= " </tr>";
			}
			$html_table_log .= "</table>";
			return array($html_table_log, $array_table_log);
		} catch(PDOException $e) {
		 echo 'ERROR: ' . $e->getMessage();
		}
	}
}
?>