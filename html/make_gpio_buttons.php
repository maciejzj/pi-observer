<?php
function make_gpio_buttons() {
	// GPIO config
	$val_array = array(0, 0);
	// Default pinout: val_array = [status_led = GPIO16, buzzer = GPIO12]
	// if needed modify in source pinout file
	require_once "gpio_pinout.php";
		//this php script generate the first page in function of the file
		for ($i = 0; $i < 1; $i++) {
			//set the pin's mode to output and read them
			system("gpio mode ".$pinout_array[$i]." out");
			exec ("gpio read ".$pinout_array[$i], $val_array[$i], $return);
		}
		//for loop to read the value
		$i = 0;
		for ($i = 0; $i < 2; $i++) {
			//if off
			if ($val_array[$i][0] == 0) {
				echo ("<img id='button_".$i."' class='gpio_button' src='data/img/idle/idle_".$i.".png' onclick='change_pin (".$i.");'/>");
			}
			//if on
			if ($val_array[$i][0] == 1 ) {
				echo ("<img id='button_".$i."' class='gpio_button' src='data/img/active/active_".$i.".png' onclick='change_pin (".$i.");'/>");
			}	 
		}
}
?>
