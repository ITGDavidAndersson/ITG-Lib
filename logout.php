<?php
session_name("ITGlib");
session_start();
unset($_SESSION["user"]);
unset($_SESSION["books"]);
if(isset($_SESSION["admin"])) {
	unset($_SESSION["admin"]);
}
header("Location: index.php");
?>