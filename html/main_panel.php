<?php
	session_start();
	
	if (!isset($_SESSION['logged'])) {
		header('Location: index.php');
		exit();
	}	

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "connect.php";
$connection = @new mysqli($host, $db_user, $db_password, $db_name_logs);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>BalloonS - Control panel</title>
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDC3AdKTRhuef-V14umy0kfEiieAi5RaFw"></script>
		
		<?php
			require_once('google_maps.php');
		?>	
		<script>var map;</script>
		<script src="google_maps.js"></script>
		<script>google.maps.event.addDomListener(window, 'load', initialize);</script>
		
	</head>
	<body>
		<div id="topbar">
			<?php echo "<div class='status'>Logged: ".$_SESSION['login'].'</div> <a class="button" id="logout" href="logout.php">logout</a>'; ?>
		</div>
		<div id="panel_container">
			<div class = "infosection">
				<div class = "infosection_title">GPIO controls</div>
				<?php
					require_once('make_gpio_buttons.php');
					make_gpio_buttons();
				?>
				</br>
				<a class="button" href='http://192.168.1.110:8081/0/'>Camera control</a>
				<a class="button" href='http://192.168.1.110:8080/0/action/snapshot'>Camera snapshot</a>
			</div>
			
			<div class = "infosection">
				<div class = "infosection_title">Camera live preview</div>
				<img style="-webkit-user-select: none; display: block; margin-left: auto; margin-right: auto; margin-bottom: 20px; margin-top: 20px; cursor: zoom-in;" src="http://91.233.72.242:8081/0/" width="640" height="480">
			</div>
			
			<div class = "infosection">
				<div class = "infosection_title">Localisation</div>
				<div id = "map_canvas">
					<div id = "map"></div>
				</div>
				<div class="log_cont">
					<a class="button" href="/sensor_logs/loc_log" download>Download log file</a>
					<div class="textlog" id="loc_log">
						<?php
							$myfile = fopen("./sensor_logs/loc_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
							?>
					</div>
				</div>
			</div>
			
			<div class="infosection">
				<div class = "infosection_title">Temperature log</div>
				<img class="chart" src="sensor_logs/temp_log.png">
				<div class="log_cont">
					<a class="button" href="/sensor_logs/temp_log" download>Download log file</a>
					<div class="textlog" id="temp_log">
						<?php
							$myfile = fopen("./sensor_logs/temp_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
						?>
					</div>
					<?php
						if ($connection->connect_errno != 0) {
								echo "Error: ".$connection->connect_errno;
						} else {
							try {
								$query = "SELECT * FROM temp_log";
								print "<table>";
								$result = $connection->query($query);
								//return only the first row (we only need field names)
								$row = $result->fetch_assoc();
								print " <tr>";
								foreach ($row as $field => $value){
								 print " <th>$field</th>";
								} // end foreach
								print " </tr>";
								//second query gets the data
								$data = $connection->query($query);
								//$data->setFetchMode(PDO::FETCH_ASSOC);
								foreach($data as $row){
								 print " <tr>";
								 foreach ($row as $name=>$value){
								 print " <td>$value</td>";
								 } // end field loop
								 print " </tr>";
								} // end record loop
								print "</table>";
							} catch(PDOException $e) {
							 echo 'ERROR: ' . $e->getMessage();
							}
						}
					?>
				</div>
			</div>
			
			<div class="infosection">
				<div class = "infosection_title">Internal temperature log</div>
				<img class="chart" src="sensor_logs/int_temp_log.png">
				<div class="log_cont">
					<a class="button" href="/sensor_logs/int_temp_log" download>Download log file</a>
					<div class="textlog" id="int_temp_log">
						<?php
							$myfile = fopen("./sensor_logs/int_temp_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
							?>
					</div>
				</div>
			</div>
			
			<div class="infosection">
				<div class = "infosection_title">Pressure log</div>
				<img class="chart" src="sensor_logs/press_log.png">
				<div class="log_cont">
					<a class="button" href="/sensor_logs/press_log" download>Download log file</a>
					<div class="textlog" id="press_log">
						<?php
							$myfile = fopen("./sensor_logs/press_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
							?>
					</div>
				</div>
			</div>
			
			<div class="infosection">
				<div class = "infosection_title">Altitude log</div>
				<img class="chart" src="sensor_logs/alt_log.png">
				<div class="log_cont">
					<a class="button" href="/sensor_logs/alt_log" download>Download log file</a>
					<div class="textlog" id="alt_log">
						<?php
							$myfile = fopen("./sensor_logs/alt_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
							?>
					</div>
				</div>
			</div>
			
			<div class="infosection">
				<div class = "infosection_title">Humidity log</div>
				<img class="chart" src="sensor_logs/hum_log.png">
				<div class="log_cont">
					<a class="button" href="/sensor_logs/hum_log" download>Download log file</a>
					<div class="textlog" id="hum_log">
						<?php
							$myfile = fopen("./sensor_logs/hum_log", "r") or die("Unable to open file!");
							// Output one line until end-of-file
							while(!feof($myfile)) {
								echo fgets($myfile) . "<br>";
							}
							fclose($myfile);
							?>
					</div>
				</div>
			</div>
			
		</div>
		
		<div id="footer">
			Maciej Ziaja 2018, maciejzj@icloud.com
		</div>
		<script src="gpio.js"></script>
		<script>
			var messageBody = document.querySelector('#temp_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
			var messageBody = document.querySelector('#int_temp_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
			var messageBody = document.querySelector('#press_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
			var messageBody = document.querySelector('#alt_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
			var messageBody = document.querySelector('#hum_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
			var messageBody = document.querySelector('#loc_log');
			messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
		</script>
	</body>
</html>