<?php
	include ("cselfunc.php");
	$q = $_GET["q"];
	$d = getItem($q);
	if ($d) {
		$name = $d["DESCRIPTION"];
		echo "<b>" . $name . "</b>";
	} else {
		echo "Item not found";
	}
?>