<?php
	session_start();
	error_reporting( E_ALL );
	if (!isset($_SESSION['registation_successful']))	{
		header('Location: index.php');
		exit();
	} else {
    unset($_SESSION['registation_successful']);
  }
?>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE HTML>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>BaloonS - Welcome</title>

	<link rel="stylesheet/less" type="text/css" href="styles.less">
	<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
</head>

<body>
	<div id="topbar">
		<div class="status">
			Pi-Observer!
		</div>
		<a class="button" id="logout" href="index.php">Go back to main page!</a>
		<br><hr id = "topbar_separator">
	</div>
<!show welcome info>
	<div id="welcome_container">
	Thank you for joining us!<br />
  You will receive an e-mail after our team accepts your request!
	</div>


	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
