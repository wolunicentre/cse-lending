<?php
	include ("cselfunc.php");
	$q = $_GET["q"];
	$d = getStudent($q);
	if ($d) {
		$name = $d['FIRST_NAME'] . " " . $d['LAST_NAME'];
		$email = $d['USER_NAME'] . "@uowmail.edu.au";
		echo "<b>" . $person_id . " " . $name . "</b> - " . $email;
	} else {
		echo "Student not found";
	}
?>