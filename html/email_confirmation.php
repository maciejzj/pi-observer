<?php
	session_start();
	error_reporting(E_ALL);

  require_once "connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  $nickname = $_GET['username']; //get user nickname from confirm link
  $code = $_GET['code']; //get confirmation code from confirm link

  try {
	//connect to database
     $connection = new mysqli($host, $db_user, $db_password, $db_name);
     if ($connection->connect_errno != 0) {
       throw new Exception(mysqli_connect_erno());
     } else {
			 //check if nickname name exist in db
			 $result = $connection->query("SELECT * FROM users WHERE login='$nickname'");
			 if(!$result) throw new Exception($connection->error);

			 $nickname_count = $result->num_rows;
			 if($nickname_count>0) {
      	  //get auth code for user from database
          while($row = mysqli_fetch_assoc($result)) {
            $db_code = $row['confirm_code'];
            $email = $row['email'];
          }

	  //check if code from link is correct
          if ($db_code == $code) {
            $result = $connection->query("UPDATE users SET confirmed='1' WHERE login='$nickname'");
     			  if(!$result) throw new Exception($connection->error);

        		$message = '
        		$nickname! Your request to join us was accepted!
            Feel free to log in http://localhost
        		';

          //TODO: sending e-mail to user
          //mail($email,"$nickname email confimation",$message,"From: DoNotReply@The_BalloonS.com");

            $_SESSION['message'] = 'Username and code does match!<br />Registration of <span class="color_emphasis_text">'.$nickname.'</span> complete!';

          } else {
            $_SESSION['message'] = 'Username and code does not match!';
        }

        }
      }
      $connection->close();
  }
  catch(Exception $e) {
    $all_OK = false;
    $_SESSION['e_connection'] = 'Server error! We apalogise for not having our services available. Please try again later!<br \>Developer info: '.$e;
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
	<title>BaloonS - Confirmed</title>

  <link rel="stylesheet/less" type="text/css" href="styles.less">
	<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.7.1/less.min.js" ></script>
</head>

<body>
	<div id="topbar">
		<div class="status">
			BalloonS!
		</div>
		<a class="button" id="logout" href="index.php">Go back to main page!</a>
		<br><hr id = "topbar_separator">
	</div>

	<div id="welcome_container">
<?php
//show message on screen
      if(isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
      }
    ?><br />
	</div>

	<div id="footer">
		Maciej Ziaja 2018, maciejzj@icloud.com <br />
		Maciej Cholewa 2018, maciej.cholewa@interia.pl
	</div>
</body>
</html>
