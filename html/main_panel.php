<?php
	// Start session check session logged varables for user.
	session_start();

	/* If user is not logged but landed him redirect to login page. */
	if (!isset($_SESSION['logged'])) {
		header('Location: index.php');
		exit();
	}

// Force refreshing cache, may be deleted after end of dev process.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "connect.php";
require_once "make_log_table.php";

/* Establish connection with logs database */
$connection = @new mysqli($host, $db_user, $db_password, $db_name_logs);

/* Universal string keys for each log mapped to database tables names. */
$db_table_names = array(
	"temperature" => "temp_log",
	"internal_temperature" => "int_temp_log",
	"pressure" => "press_log",
	"altitude" => "alt_log",
	"humidity" => "hum_log",
	"location" => "loc_log",
);

/* For each log in names of logs make a html and extract data in array. */
foreach ($db_table_names as $log_name => $db_table_name){
	list($html_table_log, $array_table_log) = makeTable($connection, $db_table_name);

	$html_table_logs[$log_name] = $html_table_log;
	$array_table_logs[$log_name] = $array_table_log;
}
?>

<script type="text/javascript">
	/* Extract JSON data for every log sent by PHP */
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
		<!-- Viewport for mobile devices -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Compatibility -->
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>BalloonS - Control panel</title>
		<!-- Link css file, css less framework and libraries -->
		<link rel="stylesheet/less" type="text/css" href="styles.less">
		<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDC3AdKTRhuef-V14umy0kfEiieAi5RaFw"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
		<script src="chart.js"></script>
		<script src="google_maps.js"></script>

		<!-- Google Maps variable -->
		<script>var map;</script>
	</head>
	<body>
		<!-- Topbar -->
		<div id="topbar">
			<!-- Logout button on topbar -->
			<?php echo "<div class='status'>Logged: ".$_SESSION['login'].'</div> <a class="button" id="logout" href="logout.php">logout</a>'; ?>
			<br><hr id = "topbar_separator">
		</div>

		<!-- Description -->
		<div id="description_container">
			Welcome to the <span class="color_emphasis_text">control panel</span>! Here you can examine data logs, locate your balloon, watch live stream and access your GPIO. You can also manually backup your <span class="color_emphasis_text">SQL logs</span> using <a target="_blank" rel="noopener noreferrer" href="https://www.monetdb.org/Documentation/UserGuide/DumpRestore">SQL dump</a>.
		</div>

		<!-- Remote controls panel -->
		<div id = "control_panel_container">
			<div class = "infosection">
				<div class = "infosection_title">Remote controls</div>
				<div id = "gpio_buttons_wrapper">
					<?php
						/* Deploy GPIO buttons */
						require_once('make_gpio_buttons.php');
						make_gpio_buttons();
					?>
					<br>
					<!-- Embed motion stream buttons -->
					<a class="button" id="cam_butt" href='http://192.168.1.110:8081/0/'>Camera control</a>
					<a class="button" id="cam_butt" href='http://192.168.1.110:8080/0/action/snapshot'>Camera snapshot</a>
				</div>
				
				<!-- Embed motion stream buttons -->
				<img id = "camera_stream" src="http://91.233.72.242:8081">
			</div>
			<hr><br>

			<!-- Google Maps location panel -->
			<div class = "infosection">
				<div class = "infosection_title">Location</div>
				<div id = "map_canvas">
					<div id = "map"></div>
				</div>
				<!-- Make Google maps -->
				<script>makeGoogleMaps(loc_log)</script>

				<!-- Make html location table -->
				<?php
					print($html_table_logs["location"]);
				?>
			</div>
			<hr><br>

			<!-- All logs with their tables and charts -->
			<div class = "infosection_title">Temperature log</div>
			<div class = "infosection">
				<div id = "temp_tabs_stack">
				<?php
					print($html_table_logs["temperature"]);
				?>
				<?php
					print($html_table_logs["internal_temperature"]);
				?>
				</div>

				<div class = "chart_wrapper", id="temp_log_chart_wrapper">
					<canvas class = "chart" id="temp_log_chart"></canvas>
				</div>
				<script>makeDoubleChart("temp_log_chart", "External temperature", "Internal temperature", temp_log, int_temp_log)</script>
			</div>
			<hr><br>

			<div class = "infosection_title">Pressure log</div>
			<div class = "infosection">
				<div class = "chart_wrapper" id = "press_log_chart_wrapper">
					<canvas class = "chart" id="press_log_chart"></canvas>
				</div>
				<script>makeChart("press_log_chart", "Pressure", press_log)</script>

				<?php
					print($html_table_logs["pressure"]);
				?>
			</div>
			<hr><br>

			<div class = "infosection_title">Altitude log</div>
			<div class = "infosection">
				<?php
					print($html_table_logs["altitude"]);
				?>

				<div class = "chart_wrapper" id ="alt_log_chart_wrapper">
					<canvas class = "chart" id="alt_log_chart"></canvas>
				</div>
				<script>makeChart("alt_log_chart", "Altitude", alt_log)</script>
			</div>
			<hr><br>

			<div class = "infosection_title">Humidity log</div>
			<div class = "infosection">
				<div class = "chart_wrapper" id = "hum_log_chart_wrapper">
					<canvas class = "chart" id="hum_log_chart"></canvas>
				</div>
				<script>makeChart("hum_log_chart", "Humidity", hum_log)</script>

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
	</body>
</html>
