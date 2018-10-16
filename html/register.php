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
        $_SESSION['e_nickname'] = "Nickname has to be 3-24 characters long!";
      }
      //checking characters
      if(ctype_alnum($nickname) == false) {
        $all_OK = false;
        $_SESSION['e_nickname'] = "Nickname has to have only alphanumeric charcters!";
      }

    //check password
    $passwd = $_POST['passwd'];
    $passwd_conf = $_POST['passwd_conf'];
      //checking length
      if((strlen($passwd)<6) || (strlen($passwd)>24)) {
        $all_OK = false;
        $_SESSION['e_passwd'] = "Password has to be 6-24 characters long!";
      }
      //checking characters
      if(preg_match('/[0-9]/',$passwd) < 1 || preg_match('/[a-z]/',$passwd) < 1 || preg_match('/\d/',$passwd) < 1) {
        $all_OK = false;
        $_SESSION['e_passwd'] = "Password has to have at least one small letter, one capital letter and one digit!";
      }
      //checking password compatibility
      if($passwd!=$passwd_conf) {
        $all_OK = false;
        $_SESSION['e_passwd_conf'] = "Passwords don't match!";
      }
    //password hashing
    $passwd_hash = password_hash($passwd, PASSWORD_DEFAULT);

    //check e-mail
    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
      if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email)) {
        $all_OK = false;
        $_SESSION['e_email'] = "Incorrect e-mail!";
      }

    //check Terms of Use acceptation
    if(!isset($_POST['terms_confirm'])) {
      $all_OK = false;
      $_SESSION['e_checkbox_terms'] = 'Read and confirm <a href="terms.php">Terms of Use</a>!';
    }

    /*TODO:check reCAPTCHA
    $sekret = "6LfzRXUUAAAAAH9UDt3ZDGnjIUjYbKowljOollBJ";
    $check = file_get_content('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
    $response = json_decode($check);
    if($response->success==false) {
      $all_OK = false;
      $_SESSION['e_bot'] = 'Confirm that you are a human!';
    }*/

    require_once "connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try {
	     $connection = new mysqli($host, $db_user, $db_password, $db_name);
       if ($connection->connect_errno != 0) {
         throw new Exception(mysqli_connect_erno());
       } else {
				 //check if nickname name exist in db
				 $result = $connection->query("SELECT id FROM users WHERE login='$nickname'");
				 if(!$result) throw new Exception($connection->error);

				 $nickname_count = $result->num_rows;
				 if($nickname_count>0) {
					 $all_OK = false;
					 $_SESSION['e_nickname'] = "We have that nickname adress in our database.";
	 			 }

         //check if e-mail name exist in db
				 $result = $connection->query("SELECT id FROM users WHERE email='$email'");
				 if(!$result) throw new Exception($connection->error);

				 $email_adresses_count = $result->num_rows;
				 if($email_adresses_count>0) {
					 $all_OK = false;
					 $_SESSION['e_email'] = "We have that e-mail adress in our database.";
	 			 }

				 //check ALL - add new user to database
				 if($all_OK == true) {
					 $auth_code = rand();
					 if($connection->query("INSERT INTO users VALUES (NULL, '$nickname', '$passwd_hash', '$email', '0', '$auth_code')")) {
						 $_SESSION['registation_successful'] = true;
						 header('Location: welcome.php');
					 } else {
						 throw new Exception($connection->error);
					 }

					 $message = '
					 Confirm that $nickname with e-mail adress: $email is allowed to join.
					 Click the link:
					 http://localhost/email_confirmation.php?username='.$nickname.'&code='.$auth_code.'
					 ';
					 /*TODO: sending e-mail confirmation to admin
					 mail(vikinkpl@gmail.com,"$nickname email confimation",$message,"From: DoNotReply@The_BalloonS.com");*/
				 }

         $connection->close();
       }
    }
    catch(Exception $e) {
      $all_OK = false;
      $_SESSION['e_connection'] = 'Server error! We apalogise for not having our services available. Please try again later!<br \>Developer info: '.$e;
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
      <br /><a href="index.php" style="text-decoration: none"><input type="submit" value="Abort!" /></a>
  </div>

	<div id="register_container">
		<form method="post">
      <?php
        if(isset($_SESSION['e_connection'])) {
          echo '<div class="error">'.$_SESSION['e_connection'].'</div>';
          unset($_SESSION['e_connection']);
        }
      ?>
      Nickname (3-24 characters, only alphanumeric characters):<br />
			<input type="text" name="nickname" placeholder="nickname" onfocus="this.placeholder=''" onblur="this.placeholder='nickname'"/> <br />
      <?php
        if(isset($_SESSION['e_nickname'])) {
          echo '<div class="error">'.$_SESSION['e_nickname'].'</div>';
          unset($_SESSION['e_nickname']);
        }
      ?><br />

<!TODO: info about requirements need to be visible after hovering over question mark>
      Password (6-24 characters, minimum one capital letter, one lowercase and one digit):<br />
      <input type="password" name="passwd" placeholder="password" onfocus="this.placeholder=''" onblur="this.placeholder='password'"/> <br />
      <?php
        if(isset($_SESSION['e_passwd'])) {
          echo '<div class="error">'.$_SESSION['e_passwd'].'</div>';
          unset($_SESSION['e_passwd']);
        }
      ?><br />
      Confirm password:<br />
      <input type="password" name="passwd_conf" placeholder="confirm password" onfocus="this.placeholder=''" onblur="this.placeholder='confirm password'"/> <br />
      <?php
        if(isset($_SESSION['e_passwd_conf'])) {
          echo '<div class="error">'.$_SESSION['e_passwd_conf'].'</div>';
          unset($_SESSION['e_passwd_conf']);
        }
      ?><br />

      E-mail adress:
      <input type="text" name="email" placeholder="e-mail" onfocus="this.placeholder=''" onblur="this.placeholder='e-mail'"/> <br />
      <?php
        if(isset($_SESSION['e_email'])) {
          echo '<div class="error">'.$_SESSION['e_email'].'</div>';
          unset($_SESSION['e_email']);
        }
      ?><br />

      <label>
        <input type="checkbox" name="terms_confirm"> I solemnly swear that I do accept <a href="terms.php">Terms of Use</a>.
      </label><br />
      <?php
        if(isset($_SESSION['e_checkbox_terms'])) {
          echo '<div class="error">'.$_SESSION['e_checkbox_terms'].'</div>';
          unset($_SESSION['e_checkbox_terms']);
        }
      ?><br />

<!--TODO:recaptcha
      <div class="g-recaptcha" data-sitekey="6LfzRXUUAAAAAFUE-IApgwaLIHdWXmJZAJcjdLvT"></div><br />
      <?php
        if(isset($_SESSION['e_bot'])) {
          echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
          unset($_SESSION['e_bot']);
        }
      ?><br />
-->
      <input type="submit" value="Sign up!" />
		</form>
	</div>

	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br/>
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
