<?php
error_reporting( E_ALL );
	session_start();
	//if passwor or login is not inserted do not log user
	if ((!isset($_POST['login'])) || (!isset($_POST['passwd']))) {
		header('Location: index.php');
		exit();
	}

	require_once "connect.php";

	$connection = @new mysqli($host, $db_user, $db_password, $db_name);
	if ($connection->connect_errno != 0) {
		echo "Error: ".$connection->connect_errno;
	}
	else {
		// Get values from form on page
		$login = $_POST['login'];
		$passwd = $_POST['passwd'];
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");

		// Get data for the login
		if ($rezultat = @$connection->query(
		sprintf("SELECT * FROM users WHERE login='%s'",
		mysqli_real_escape_string($connection, $login)))) {
			$usr_count = $rezultat->num_rows;
			if($usr_count > 0) {
				// Check if user account is active
				$row = $rezultat->fetch_assoc();
				$active = $row['active'];
				if($active == '0') { // If no do not log user in
					$_SESSION['error'] =
						'<div class="error">Wait for confirmation!</div>';
					header('Location: index.php');
				} else { // Else check if password is correct
					if (password_verify($passwd, $row['pass'])) { 
						// Log user in if password is correct
						$_SESSION['logged'] = true;
						$_SESSION['id'] = $row['id'];
						$_SESSION['login'] = $row['login'];

						unset($_SESSION['error']);
						$rezultat->free_result();

						header('Location: main_panel.php');
					}
					else {
						$_SESSION['error'] = 
							'<div class="error">Invalid credentials!</div>';
						header('Location: index.php');
					}
				}
			} else {
				$_SESSION['error'] = 
					'<div class="error">Invalid credentials!</div>';
				header('Location: index.php');
			}
		}
		$connection->close();
	}
?>

