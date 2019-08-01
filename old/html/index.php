<?php
	// Start session check session logged varables for user.
	session_start();
	error_reporting( E_ALL );
	if ((isset($_SESSION['logged'])) && ($_SESSION['logged']==true))
	{
		/* If logged user session is saved redirect him to panel
		 * he doesn't need to see login page again.
		 */
		header('Location: main_panel.php');
		exit();
	}
?>

<?php
// Force refreshing cache, may be deleted after end of dev process.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE HTML>
<head>
	<!-- Viewport for mobile devices -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Compatibility -->
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>BaloonS - High-altitude balloon</title>

	<!-- Link css file and css less framework -->
	<link rel="stylesheet/less" type="text/css" href="styles.less">
	<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
</head>

<body>
	<!-- Topbar -->
	<div id="topbar">
		<div class="status">
			BalloonS!
		</div>
		<!-- Register button on topbar -->
		<a class="button" id="logout" href="register.php">register</a>
		<br><hr id = "topbar_separator">
	</div>

	<!-- Description -->
	<div id="description_container">
		BalloonS is a <span class="color_emphasis_text">high-altitude atmospheric ballon</span> project. It provides logging services and remote access to aid gathering flight information and search after landing of the balloon. The system is based on <span class="color_emphasis_text">Raspberry Pi</span>. It is easy deployable and accessible, you can visit our <a target="_blank" rel="noopener noreferrer" href="https://github.com/MaciejZj/balloonS">GitHub</a> and download the project to launch your own balloon.
	</div>

	<!-- Login pane with sign in inputs -->
	<div id="login_container">
	Control panel login<br>
	<hr>
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
		Maciej Ziaja maciejzj@icloud.com <br/>
		Maciej Cholewa maciej.cholewa@interia.pl
	</div>
</body>
</html>
