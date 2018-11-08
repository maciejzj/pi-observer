<?php
	session_start();
	error_reporting( E_ALL );
	if ((isset($_SESSION['logged'])) && ($_SESSION['logged']==true))
	{
		header('Location: main_panel.php');
		exit();
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
	<title>BaloonS - High-altitude balloon</title>

	<link rel="stylesheet/less" type="text/css" href="styles.less">
	<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
</head>

<body>
	<div id="topbar">
		<div class="status">
			BalloonS!
		</div>
		<a class="button" id="logout" href="register.php">register</a>
		<br><hr>
	</div>
	
	<div id="login_container">
	Control panel login:<br />
		<form action="login.php" method="post">
			<input type="text" name="login" placeholder="login" onfocus="this.placeholder=''" onblur="this.placeholder='login'"/>
			<input type="password" name="passwd" placeholder="password" onfocus="this.placeholder=''" onblur="this.placeholder='password'"/> <br />
			<?php
				if(isset($_SESSION['error']))	echo $_SESSION['error'];
			?>
			<input type="submit" value="Log in" />
		</form>
	</div>

	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
