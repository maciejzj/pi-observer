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
require_once "make_log_table.php";

$connection = @new mysqli($host, $db_user, $db_password, $db_name_logs);

$db_table_names = array(
	"temperature" => "temp_log",
	"internal_temperature" => "int_temp_log",
	"pressure" => "press_log",
	"altitude" => "alt_log",
	"humidity" => "hum_log",
	"location" => "loc_log",
);

foreach ($db_table_names as $log_name => $db_table_name){
	list($html_table_log, $array_table_log) = makeTable($connection, $db_table_name);
	
	$html_table_logs[$log_name] = $html_table_log;
	$array_table_logs[$log_name] = $array_table_log;
}
?>

<script type="text/javascript">
	var temp_log = <?php echo json_encode($array_table_logs["temperature"], JSON_PRETTY_PRINT) ?>;
	var int_temp_log = <?php echo json_encode($array_table_logs["internal_temperature"], JSON_PRETTY_PRINT) ?>;
	var loc_log = <?php echo json_encode($array_table_logs["location"], JSON_PRETTY_PRINT) ?>;
	var press_log = <?php echo json_encode($array_table_logs["pressure"], JSON_PRETTY_PRINT) ?>;
	var alt_log = <?php echo json_encode($array_table_logs["altitude"], JSON_PRETTY_PRINT) ?>;
	var hum_log = <?php echo json_encode($array_table_logs["humidity"], JSON_PRETTY_PRINT) ?>;
</script>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>BalloonS - Control panel</title>
		<link rel="stylesheet/less" type="text/css" href="styles.less">
		<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDC3AdKTRhuef-V14umy0kfEiieAi5RaFw"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
		<script src="chart.js"></script>
		<script src="google_maps.js"></script>

		<script>var map;</script>
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
				<br>
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
				<script>makeGoogleMaps(loc_log)</script>
				
				<div class="log_cont">
					<a class="button" href="/sensor_logs/loc_log" download>Download log file</a>

						<?php
							print($html_table_logs["location"]);
						?>
				</div>
			</div>

			<div class="infosection">
				<div class = "infosection_title">Temperature log</div>
				
				<canvas id="temp_log_chart"></canvas>
				<script>makeDoubleChart("temp_log_chart", "External temperature", "Internal temperature", temp_log, int_temp_log)</script>
				
				<div class="log_cont">
					<a class="button" href="/sensor_logs/temp_log" download>Download log file</a>

						<?php
							print($html_table_logs["temperature"]);
						?>
				</div>
			</div>

			<div class="infosection">
				<div class = "infosection_title">Internal temperature log</div>

				<div class="log_cont">
					<a class="button" href="/sensor_logs/int_temp_log" download>Download log file</a>

						<?php
							print($html_table_logs["internal_temperature"]);
						?>

				</div>
			</div>

			<div class="infosection">
				<div class = "infosection_title">Pressure log</div>
				
				<canvas id="press_log_chart"></canvas>
				<script>makeChart("press_log_chart", "Pressure", press_log)</script>
				
				<div class="log_cont">
					<a class="button" href="/sensor_logs/press_log" download>Download log file</a>

				</div>
					<?php
						print($html_table_logs["pressure"]);
					?>
			</div>

			<div class="infosection">
				<div class = "infosection_title">Altitude log</div>
				
				<canvas id="alt_log_chart"></canvas>
				<script>makeChart("alt_log_chart", "Altitude", alt_log)</script>
				
				<div class="log_cont">
					<a class="button" href="/sensor_logs/alt_log" download>Download log file</a>
				</div>
					<?php
						print($html_table_logs["altitude"]);
					?>
			</div>

			<div class="infosection">
				<div class = "infosection_title">Humidity log</div>

				<canvas id="hum_log_chart"></canvas>
				<script>makeChart("hum_log_chart", "Humidity", hum_log)</script>

				<div class="log_cont">
					<a class="button" href="/sensor_logs/hum_log" download>Download log file</a>
				</div>

					<?php
						print($html_table_logs["humidity"]);
					?>

			</div>

		</div>

		<div id="footer">
			Maciej Ziaja 2018, maciejzj@icloud.com <br/>
			Maciej Cholewa 2018, maciej.cholewa@interia.pl
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
