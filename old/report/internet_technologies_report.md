# balloonS

A remote control and sensor data visualization for Raspberry Pi based high altitude balloon.
  
[TOC]

## Introduction
### Brief description
The system provides location, temperature, humidity, pressure and altitude logging with camera streaming and additional GPIO remote controls with a web server hosted on a mobile platform.
### Detailed description
The goal of this project is to make and build a microsystem that will be then placed inside of capsule carried by a high altitude balloon. The system will be powered by Raspberry Pi and gather information about conditions in different parts of atmosphere. All the data will be presented and accessible via web server hosted by the Pi and backed up evenly.

The Internet Technologies part of this program is crucial because of three reasons:

1. The balloon has to be recovered after landing, therefore the system is about to provide location for the landing zone, so the balloon can be easily found. This is achieved by three developements:
 
 	* GPS logging and presenting it by Google maps on hosted website
 	* Providing a camera livestream
 	* Activating buzzer accessed via the hosted website
 	
1. The gathered data should be backed up and remotely accessible in case of the recovery being not viable. In this scenario even if the hardware is lost the data will be gained.
1. The gathered data should be presented in a readable form, easy to process and analyze. Therefore the hosted server ought to provide user-friendly interface with interactive charts and neatly formatted tables.

## The aim and the scope of the project
### The initial status of the project during startup
The project has been started before the beginning of the Internet Technologies subject. At the startup the system has fully functioning sensor hardware and contains:

* $I^2C$ based humidity and pressure sensors as well as RTC module
* GPS connected via *UART*
* Two thermometers connected via *1-wire*
* Web server based on Apache2, with MySQL user data base
* Webcam streaming video using motion
* Functioning remote GPIO controls accessed by the web page
* Web page presenting the data in a non interactive way, charts are images and logs are row data
* Embedded Google Maps window with route markers

Since the sensors hardware and offline back-end (made in Python) are not the subject of Internet Technologies they will not be mentioned again. The progress of this part of the project, not related to web based services, can be tracked on GitHub.

### The enhancements that will be implemented during the Internet Technologies project
The aim of the enhancements is to make web page more interactive and provide easier way to analyze data. The logging system will also be upgraded. During Internet Technologies project we are about to:

1. Create a system for registering new users. A person who will be in need of having an account will be able to register. It will cause sending an e-mail to the administrator who will have to confirm the registration.
1. Gather the sensor logs in MySQL database rather than inside text files, the tables presenting data should be nicely embedded on the web page.
1. Generate interactive log charts on client's side by a JavaScript library.
1. Make the site adaptive to mobile devices.

## Schedule of work
### Planned schedule of work

|    date    | task number |                              planned task description                             |
|:----------:|:-----------:|:---------------------------------------------------------------------------------:|
| 2018-10-10 |      W1     | schedule and goals determination                                                  |
| 2018-10-17 |      W2     | new user registration with admin e-mail confirmation                              |
| 2018-10-24 |      W3     | storing data in both MySQL and text files                                         |
| 2018-10-31 |      W4     | presenting the data in tables embedded on the website (data stored in MySQL only) |
| 2018-11-07 |      W5     | interactive log charts on client's site                                           |
| 2018-11-14 |      W6     | tables, charts CSS styling, desktop version polishing                             |
| 2018-11-21 |      W7     | mobile adaptation - mobile version of the web page 1                              |
| 2018-11-28 |      W8     | mobile adaptation - mobile version of the web page 2                              |
| 2018-12-05 |      W9     | extra time (for unpredicted delays)                                                |

### Schedule realization
#### Week 1
The introductory part of this document was created alongside with planned schedule.
#### Week 2
We managed to split the work what resulted in a rapid progress of work, some goals reaching W4 were achieved.

Now the logged data from all sensors is stored in sql database. The communication between database and python is achieved with the `mysql.connector` module. We will show how it is used on the example of pressure logging:
```python
degrees = sensor.read_temperature()
pascals = sensor.read_pressure()
hectopascals = pascals / 100

mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

mycursor = mydb.cursor()
sql = "INSERT INTO press_log(log_time, log_val, unit) VALUES (%s, %s, %s)"
val = (time_stamp, hectopascals, "hPa")
mycursor.execute(sql, val)
mydb.commit()
```
The crucial parts of communication are `connect()` and `execute()` functions. First of them is rather self-explanatory, the second works like `printf`, it creates query based on `sql` string with values inserted from `val` array.

To implement this kind of logging temperature reading should be achieved via python script instead using bash with sed. Here is the new temperature reading script:

```python
#!/usr/bin/python
import sys
import re
import mysql.connector
import datetime as dt
	
therm_dev_name = {'ext' : "28-000005945f57", 'int' : "28-00000a418b77"}
therm_addr = {'ext' : "/sys/bus/w1/devices/" + therm_dev_name['ext'] + "/w1_slave",
				"int" : "/sys/bus/w1/devices/" + therm_dev_name['int'] + "/w1_slave"
				}
therm_log_name = {'ext': "temp_log", 'int': "int_temp_log"}

mydb = mysql.connector.connect(
	host = "localhost",
	user = "root",
	passwd = "balloonSroot",
	database = "balloonS"
)

for key in therm_addr.viewkeys() & therm_log_name.viewkeys():
	with open(therm_addr[key], mode = "r") as therm:	
		lines = therm.readlines()
		for line in lines:
			match = re.search(r'(?<=t=)[0-9]*', line)
			if(match):
				temp = float(match.group()) / 1000

	time_stamp = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
		
	mycursor = mydb.cursor()
	sql = "INSERT INTO " + therm_log_name[key] + "(log_time, log_val, unit) VALUES (%s, %s, %s)"
	val = (time_stamp, temp, "C")
	mycursor.execute(sql, val)
	mydb.commit()
```
The parametrisation of thermometers addresses is preserved using dictionaries, so in the future they will be placed in seprate files. Those files will be easily edited by user to allow him to enter the address of his unique thermometer.

To make the setup easier and eventually fully automated the database creation is achieved by sql script:

```sql
DROP USER 'root'@'localhost';
CREATE USER 'root'@'localhost' IDENTIFIED BY 'balloonSroot';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost'

CREATE DATABASE balloonS_users;
USE balloonS_users;

CREATE TABLE users(id int, login varchar(25), pass varchar(25));

CREATE DATABASE balloonS;
USE balloonS;

CREATE TABLE press_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE alt_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE temp_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE int_temp_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE hum_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, log_val real, unit varchar(6), PRIMARY KEY (num));

CREATE TABLE loc_log(num int NOT NULL AUTO_INCREMENT, log_time timestamp, latitude real, longitude real, velocity real, course real, PRIMARY KEY (num));
```
In the future the password should be treated as variable and entered by user.

The whole system of logging data works, which can be checked by selecting everything from correct tables. Here is the example of humidity log:
```
MariaDB [balloonS]> select * from hum_log;
+------+---------------------+---------+------+
| num  | log_time            | log_val | unit |
+------+---------------------+---------+------+
|    1 | 2018-10-15 20:29:02 |    61.5 | hPa  |
|    2 | 2018-10-15 20:30:01 |    61.7 | hPa  |
|    3 | 2018-10-15 20:31:03 |    61.7 | hPa  |
|    4 | 2018-10-15 20:32:01 |    61.8 | hPa  |
|    5 | 2018-10-15 20:33:02 |    61.9 | hPa  |
|    6 | 2018-10-15 20:34:01 |    61.9 | hPa  |
|    7 | 2018-10-15 20:35:02 |    61.8 | hPa  |
|    8 | 2018-10-15 20:36:01 |    61.6 | hPa  |
```
Now the data should be gained by php when `main_panel.php` is opened and placed in html tables. Since we have to create a few of these tables the way of achieving this should be universal. Therefore we should create a function that can create any of these tables given the sql table name. The function looks like this:
```php
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
				foreach ($row as $name=>$value){
					print " <td>$value</td>";
				}
				print " </tr>";
			}
			print "</table>";
		} catch(PDOException $e) {
		 echo 'ERROR: ' . $e->getMessage();
		}
	}
}
?>
```
It is stored in a separate file to maintain order and clearance. The connection to the database is established in main file:
```php
<?php
require_once "connect.php";
require_once "make_log_table.php";
$connection = @new mysqli($host, $db_user, $db_password, $db_name_logs);
$tableLogNames = array(
	"temperature" => "temp_log",
	"internal_temperature" => "int_temp_log",
	"pressure" => "press_log",
	"altitude" => "alt_log",
	"humidity" => "hum_log",
	"location" => "loc_log",
);
?>
```
The connection data is also stored in separate file `connect.php`. This is how the function is called inside the main file:
```php
<div class="log_table">
	<?php
		makeTable($connection, $tableLogNames["location"]);
	?>
</div>
```
To make coding more convinient the names of the tables in the sql database are stored in a dictionary that is accessible to the person reading the main file.

Finally we can check the results of the work. The creation works for all of the logs' tables, regardless of their dimensions. Here is an example of the temperature log:
![html_table](https://i.imgur.com/wOYeaGC.jpg)

The registration system is under construction, it now features passwords hashing, however the email confirmation is still to be finished. This is due to the time consuming setup process on second computer, which had to be done before starting the main part od the work.

#### Week 3
During previous week we managed to get the log data from the SQL database. Now we have to pass it to javascript to make a chart from it. In the first step we decided to separate extraction of the data from drawing the HTML table. In order to do so the functions were modified in this way:
```php
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
```
As you can see, now we are creating a `string $html_table_log` variable that holds the HTML table text. This string is then returned. The function call now looks like this:
```php
<?php
foreach ($db_table_names as $log_name => $db_table_name){
	list($html_table_log, $array_table_log) = makeTable($connection, $db_table_name);
	
	$html_table_logs[$log_name] = $html_table_log;
	$array_table_logs[$log_name] = $array_table_log;
}
?>
```
And then we can deploy the table by simply calling:
`<?php print($html_table_logs["temperature"]); ?>`.

You have probably noticed that alongside the creation of the HTML table the function also prepares and returns a php array containing the data extracted from SQL database table. Notice how the function returns two values by passing an array. Now we will focus on passing the returned array of data to javascript. It is achieved by using JSON encoding:
```javasript
<script type="text/javascript">
	var temp_log = <?php echo json_encode($array_table_logs["temperature"], JSON_PRETTY_PRINT) ?>;
	var int_temp_log = <?php echo json_encode($array_table_logs["internal_temperature"], JSON_PRETTY_PRINT) ?>;
	var loc_log = <?php echo json_encode($array_table_logs["location"], JSON_PRETTY_PRINT) ?>;
	var press_log = <?php echo json_encode($array_table_logs["pressure"], JSON_PRETTY_PRINT) ?>;
	var alt_log = <?php echo json_encode($array_table_logs["altitude"], JSON_PRETTY_PRINT) ?>;
	var hum_log = <?php echo json_encode($array_table_logs["humidity"], JSON_PRETTY_PRINT) ?>;
</script>
```
We decided not to do this by `for` statement, since it would require simultaneous looping through two languages with a shared key.

Now the javascript has all the data, but we are still not ready to plot it. We have to decode the JSON objects and extract proper fields from it. For the clearance we will do all od this in separate file and dedicated functions.

The crucial function for out task is:
```javascript
function makeChart(chartID, chartLabel, log_data) {
  [xData, yData] = extractData(log_data);
  drawChart(chartID, chartLabel, xData, yData);
}
```
It contains calls to two other functions: data extracting and chart drawing. Let\`s look at `extractData()`:
```jsavascript
function extractData(data) {
  var dataArray = [];
  var timeArray = [];
  
  
  for (var i = 0; i < data.length; i++) {
    dataArray.push(JSON.parse(data[i].log_val));
    timeArray.push(JSON.stringify(data[i].log_time).replace(/['"]+/g, ''));
  }
  
  return [timeArray, dataArray];
}
```
It gets the JSON object and loops thorough it extracting timestamps and log data. Then both of them are returned.

Now we can call the chart drawing function:
```javascript
unction drawChart(chartID, chartLabel, xData, yData) {
  
  const myChart = document.getElementById(chartID).getContext('2d');

  let chart1 = new Chart(myChart, {
    type:'line', //bar, horizontalBar, pie, line, doughnut, radar, polarArea
    data:{
      labels:xData,
      datasets:[
        {
        label: chartLabel,
        fill:false,
        borderColor:'428bca',
        lineTension:0.2,
        data: yData}
      ],
    },
    options:{
      title:{
        display:true,
        text:(chartLabel + " log"),
        fontSize:25,
        fontColor:'#000'
      },
      elements: {
        point:{
          radius: 0
        }
      },
      legend:{
        display:true,
        position:'top'
      },
      layout:{
        padding:{
          left:50,
          right:50,
          bottom:50,
          top:50
        }
      }
    }
  });
}
```
And feed it with the data kept in the arrays. To plot the data versus time we should keep in mind correct date string format, which is `YYYY-MM-DD HH:MM:SS`.

Finally we can call the `makeChart()` function in our main panel:
```
<canvas id="hum_log_chart"></canvas>
				<script>makeChart("hum_log_chart", "Humidity", hum_log)</script>
```
If we keep the correct naming which is ensured by the associative array at the beginning of the file we can reuse this function for every chart we want to draw.

Here\`s the generated chart:
![chart_example](https://i.imgur.com/tCvHGVi.png)

Another feature we can now call finished is registration system. Inquisitive people can fill a form on the site, then administrator will receive an e-mail informing him about the request.
Here is how the registration form looks right now:
![alt text](https://i.imgur.com/BNeCa8d.png "registration_system_look")
The small question marks next to "Nickname:" and "Password:" show requirements that have to be fulfilled after being hovered. The `.tooltip` class that provides showing that tooltips is made in CSS language.
```css
.tooltip
{
	position: relative;
	z-index: 20;
}

.tooltip span
{
	display: none;
}

.tooltip:hover
{
	z-index: 21;
}

.tooltip:hover span
{
	display: block;
	width: 290px;
	padding: 5px;
	color: #FFF;
	background: #535663;
	text-decoration: none;
	position: absolute;
	border-radius: 6px;
	margin-left: auto;
	left: auto;
	top: 25px;
}
```
And used in php file:
```php
<a href="#" class="tooltip"><img style="float:right" src="./data/img/question_mark.png" width="6%" height="6%"><span>3-24 characters long, only alphanumeric characters, username has to be unique</span></a>
```

Valid form checks:
* length of nickname
* if nickname is unique
* if nickname has only alphanumeric characters
* length of password
* if password has at least one digit, one lowercase and one uppercase
* compatibility of password typed two times
* e-mail format
* if e-mail is unique
* if the checkbox with Terms of Use acceptation was marked
* if the user is a human (Google reCAPTCHA)

If the requirements are not fulfilled the info about it is being displayed under form inputs:
![text alt](https://i.imgur.com/oHPUhd7.png "registration_system_look2")
Nickname and e-mail address, when input correctly, are stored in `$_SESSION` variable so if new user does not fulfill correctly rest of the requirements, he does not have to type them again.
```php
<input type="text" name="nickname" placeholder="nickname" onfocus="this.placeholder=''" onblur="this.placeholder='nickname'" value="<?php echo $_SESSION['nickname']?>"/>
```

Every new user needs to be confirmed by one of administrators. It's provided by sending an activation link. 
```php
<?php
$message = 'Confirm that $nickname with e-mail adress: $email is allowed to join.
			Click the link:
			http://localhost/email_confirmation.php?username='.$nickname.'&code='.$auth_code;
			mail($admin_email,"$nickname email confimation",$message,"From: DoNotReply@TheBalloonS.com");
?>
```

Activation link changes confirmation flag stored in database from `0` to `1` with query `UPDATE users SET confirmed='1' WHERE login='$nickname'`. Then site sends an e-mail to new user in which he/she is being informed about acceptance of his/her request.