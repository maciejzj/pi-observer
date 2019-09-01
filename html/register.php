<?php
	session_start();
	error_reporting( E_ALL );
	if(isset($_POST['nickname'])) {

		// Remember data in form
		$_SESSION['nickname'] = $_POST['nickname'];
		$_SESSION['email'] = $_POST['email'];

		$all_OK = true;

		// Check nickname
		$nickname = $_POST['nickname'];
			//checking length
			if((strlen($nickname) < 3) || (strlen($nickname) > 24)) {
				$all_OK = false;
				$_SESSION['e_nickname'] = 
					"Nickname has to be 3-24 characters long!";
			}
			// Check characters
			if(ctype_alnum($nickname) == false) {
				$all_OK = false;
				$_SESSION['e_nickname'] = 
					"Nickname has to have only alphanumeric charcters!";
			}

		// Check password
		$passwd = $_POST['passwd'];
		$passwd_conf = $_POST['passwd_conf'];
			// Checking length
			if((strlen($passwd) < 6) || (strlen($passwd) > 24)) {
				$all_OK = false;
				$_SESSION['e_passwd'] = 
					"Password has to be 6-24 characters long!";
			}
			// Check characters
			if(preg_match('/[0-9]/',$passwd) < 1 || 
			   preg_match('/[a-z]/',$passwd) < 1 || 
			   preg_match('/\d/',$passwd) < 1) {
				$all_OK = false;
				$_SESSION['e_passwd'] = 
					"Password has to have at least one small letter, 
					one capital letter and one digit!";
			}
			// Check password compatibility
			if($passwd!=$passwd_conf) {
				$all_OK = false;
				$_SESSION['e_passwd_conf'] = "Passwords don't match!";
			}
		// Password hashing
		$passwd_hash = password_hash($passwd, PASSWORD_DEFAULT);

		// Check e-mail
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
			if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || 
			   ($emailB!=$email)) {
				$all_OK = false;
				$_SESSION['e_email'] = "Incorrect e-mail!";
			}

		// Check reCAPTCHA
		$secret = "6LdwB7YUAAAAAAuI1DlxyRVR99f9L3h_ix2cz13D";
		$check = file_get_contents(
			'https://www.google.com/recaptcha/api/siteverify?secret=' .
			$secret.'&response='.$_POST['g-recaptcha-response']);
		$response = json_decode($check);
		if($response->success==false) {
			$all_OK = false;
			$_SESSION['e_bot'] = 'Confirm that you are a human!';
		}

		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);

		try { 
			$connection = new mysqli($host, $db_user, $db_password, $db_name);
			if ($connection->connect_errno != 0) {
				throw new Exception(mysqli_connect_erno());
			} else {
				// Check if nickname name exist in db
				$result = $connection->query(
					"SELECT id FROM users WHERE login='$nickname'");
				if(!$result) throw new Exception($connection->error);

				$nickname_count = $result->num_rows;
				if($nickname_count>0) {
					$all_OK = false;
					$_SESSION['e_nickname'] = 
						"An account with this nickname already exists.";
	 			 }

				// Check if e-mail name exist in db
				$result = $connection->query(
					"SELECT id FROM users WHERE email='$email'");
				if(!$result) throw new Exception($connection->error);

				 $email_addresses_count = $result->num_rows;
				 if($email_addresses_count>0) {
					$all_OK = false;
					$_SESSION['e_email'] = 
						"Account with this e-mail already exists.";
	 			 }

				// Check ALL - add new user to database
				if($all_OK == true) {
					$auth_code = rand(100000000, 999999000);

					if($connection->query(
						"INSERT INTO users VALUES (
							NULL, '$nickname', '$passwd_hash',
							'$email', '1', '$auth_code')")) {
						$_SESSION['registation_successful'] = true;
						header('Location: welcome.php');
					} else {
						throw new Exception($connection->error);
					}
				}
				$connection->close();
			}
		}
		catch(Exception $e) {
			$all_OK = false;
			$_SESSION['e_connection'] = 
				'Server error!
				Please try again later!<br \>Developer info: '. $e;
		}
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
	<title>BaloonS - Registration</title>

	<link rel="stylesheet/less" type="text/css" href="styles.less">
	<script src="
		//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" >
	</script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
	<div id="topbar">
		<div class="status">
			Pi-Observer!
		</div>
		<a class="button" id="logout" href="index.php">cancel</a>
		<br><hr id = "topbar_separator">
	</div>

	<div id="register_container">
		<form method="post">
			<?php
				if(isset($_SESSION['e_connection'])) {
					echo '<div class="error">'.$_SESSION['e_connection'].
					'</div>';
					unset($_SESSION['e_connection']);
				}
			?>
			
			Nickname:
			<a href="#" class="tooltip">
			<img style="float:right"
				src="./data/img/question_mark.png" width="6%" height="6%">
			<span>3-24 characters long, only alphanumeric characters,
			username has to be unique
			</span></a><br />
			<input type="text" name="nickname"
				placeholder="nickname" 
				onfocus="this.placeholder=''"
				onblur="this.placeholder='nickname'" 
				value="<?php echo $_SESSION['nickname']?>"/>
			<br />
			<?php
				if(isset($_SESSION['e_nickname'])) {
					echo '<div class="error">'.$_SESSION['e_nickname'].'</div>';
					unset($_SESSION['e_nickname']);
				}
			?><br />

			Password:
			<a href="#" class="tooltip"><img style="float:right" 
			src="./data/img/question_mark.png" width="6%" height="6%">
			<span>6-24 characters long, minimum one capital letter,
			one lowercase and one digit
			</span></a><br />
			<input type="password" name="passwd" 
				placeholder="password"
				onfocus="this.placeholder=''"
				onblur="this.placeholder='password'"/>
			<br />
			<?php
				if(isset($_SESSION['e_passwd'])) {
					echo '<div class="error">'.$_SESSION['e_passwd'].'</div>';
					unset($_SESSION['e_passwd']);
				}
			?><br />
			Confirm password:<br />
			<input type="password" name="passwd_conf"
				placeholder="confirm password"
				onfocus="this.placeholder=''"
				onblur="this.placeholder='confirm password'"/>
				<br />
			<?php
				if(isset($_SESSION['e_passwd_conf'])) {
					echo '<div class="error">'.$_SESSION['e_passwd_conf'].
					'</div>';
					unset($_SESSION['e_passwd_conf']);
				}
			?><br />

			E-mail address:
			<input type="text" name="email" placeholder="e-mail"
				onfocus="this.placeholder=''"
				onblur="this.placeholder='e-mail'"
				value="<?php echo $_SESSION['email']?>"/>
				<br />
			<?php
				if(isset($_SESSION['e_email'])) {
					echo '<div class="error">'.$_SESSION['e_email'].'</div>';
					unset($_SESSION['e_email']);
				}
			?><br />

			<div class="g-recaptcha"
				data-sitekey="6LdwB7YUAAAAAL5XAJUR0bRIimo812j8BhiHCaiS">
			</div>
			<?php
				if(isset($_SESSION['e_bot'])) {
					echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
					unset($_SESSION['e_bot']);
				}
			?><br />

			<input type="submit" value="Sign up!" />
		</form>
	</div>

	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>

