<?php
	session_start();
	error_reporting( E_ALL );
  if (isset($_POST['nickname'])) {
    $all_OK = true;

    //check Nickname
    $nickname = $_POST['nickname'];
      //checking length
      if((strlen($nickname)<3) || (strlen($nickname)>24)) {
        $all_OK = false;
        $_SESSION['e_nickname_length'] = "Nickname has to be 3-24 characters long!";
      }

    //check password

    //check e-mail

    //check Terms of Use acceptation

    //check reCAPTCHA

    //check ALL - add new user to database
    if($all_OK == true) {
      echo "Data correct!";
      exit();
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
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>BaloonS - Registration</title>

	<link rel="stylesheet" href="style.css" type="text/css" />
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
	<div id="topbar">
		<div class="status">
			BalloonS!
		</div>
	</div>

  <div id="register_hyperlink">
      <br /><a href="index.php">Abort!</a>
  </div>

	<div id="register_container">
		<form method="post">
      Nickname (3-24 characters):<br />
      <?php
        if(isset($_SESSION['e_nickname_length'])) {
          echo '<div class="error">'.$_SESSION['e_nickname_length'].'</div>';
          unset($_SESSION['e_nickname_length']);
        }

      ?>
			<input type="text" name="nickname" placeholder="nickname" onfocus="this.placeholder=''" onblur="this.placeholder='nickname'"/> <br /><br />

<!TODO: info about requirements need to be visible after hovering over question mark>
      Password (3-24 characters, minimum one capital letter, one lowercase and one digit):<br />
      <input type="password" name="passwd" placeholder="password" onfocus="this.placeholder=''" onblur="this.placeholder='password'"/> <br /><br />
      Confirm password:<br />
      <input type="password" name="passwd_conf" placeholder="confirm password" onfocus="this.placeholder=''" onblur="this.placeholder='confirm password'"/> <br /><br />

      E-mail adress:
      <input type="text" name="email" placeholder="e-mail" onfocus="this.placeholder=''" onblur="this.placeholder='e-mail'"/> <br /><br />

      <label>
        <input type="checkbox" name="terms_confirm"> I solemnly swear that I do accept <a href="terms.php">Terms of Use.</a>
      </label><br /><br />

      <div class="g-recaptcha" data-sitekey="6LfzRXUUAAAAAFUE-IApgwaLIHdWXmJZAJcjdLvT"></div>

      <input type="submit" value="Sign up!" />
		</form>
	</div>

	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
