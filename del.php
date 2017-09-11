<?php
session_name("ITGlib");
session_start();
$sw = false;
if(isset($_SESSION["books"])) {
	foreach($_SESSION["books"] as $k => $v) {
		if($sw === false) {
			if($v === $_GET["c"]) {
				unset($_SESSION["books"][$k]);
				$sw = true;
			}
		}
	}
}
header("Location: signout.php");
?>