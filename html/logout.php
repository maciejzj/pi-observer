<?php
//log user out
	session_start();
	session_unset();
	header('Location: index.php');
?>

