<?php
function makeTable($connection, $tableName) {
	if ($connection->connect_errno != 0) {
			echo "Error: ".$connection->connect_errno;
	} else {
		try {
			$query = "SELECT * FROM " . $tableName;
			print "<table>";
			$result = $connection->query($query);
			// We want the first row for col names
			$row = $result->fetch_assoc();
			print " <tr>";
			foreach ($row as $field => $value){
				print " <th>$field</th>";
			}
			print " </tr>";

			// Print actual data
			foreach($result as $row){
				print " <tr>";
				$returnArray[] = $row;
				foreach ($row as $name=>$value){
					print " <td>$value</td>";
				}
				print " </tr>";
			}
			print "</table>";
			return $returnArray;
		} catch(PDOException $e) {
		 echo 'ERROR: ' . $e->getMessage();
		}
	}
}
?>