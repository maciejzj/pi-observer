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
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>BaloonS - Welcome</title>

	<link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body>
	<div id="topbar">
		<div class="status">
			BalloonS!
		</div>
	</div>

	<div id="register_hyperlink">
      <br /><a href="index.php" style="text-decoration: none"><input type="submit" value="Go back to main page" /></a>
	</div>

	<div id="register_container">
	Thank you for joining us!<br />
  You will receive an e-mail after our team accepts your request!
	</div>


	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
