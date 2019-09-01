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
	<title>Pi-Observer</title>

	<!-- Link css file and css less framework -->
	<link rel="stylesheet/less" type="text/css" href="styles.less">
	<script 
		src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" >
	</script>
</head>

<body>
	<!-- Topbar -->
	<div id="topbar">
		<div class="status">
			Pi-Observer!
		</div>
		<!-- Register button on topbar -->
		<a class="button" id="logout" href="register.php">register</a>
		<br><hr id = "topbar_separator">
	</div>

	<!-- Description -->
	<div id="description_container">
		Pi-Observer is a <span class="color_emphasis_text">
		universal data logger and remote access</span> project.
		It can be mounted on any mobile or stationary system.
		The project is based on
		<span class="color_emphasis_text">Raspberry Pi</span>.
		It is easy to deploy and use, you can visit the project's
		<a target="_blank" rel="noopener noreferrer" 
			href="https://github.com/MaciejZj/Pi-Observer">GitHub
		</a>
		site and install it on your own Raspberry Pi.
	</div>

	<!-- Login pane with sign in inputs -->
	<div id="login_container">
	Control panel login<br>
	<hr>
		<form action="login.php" method="post">
			<input type="text" name="login" 
				placeholder="login"
				onfocus="this.placeholder=''"
				onblur="this.placeholder='login'"/>
			<input type="password" name="passwd"
				placeholder="password"
				onfocus="this.placeholder=''"
				onblur="this.placeholder='password'"/>
			<br/>
			<?php
				if(isset($_SESSION['error']))
				{
					echo $_SESSION['error'];
				}
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
