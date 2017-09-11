<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
//require("inc/code.php");
if(isset($_SESSION["books"])) {
	if(count($_SESSION["books"]) !== 0) {
		$err = false;
		foreach($_SESSION["books"] as $v) {
			$q = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
			$ok = $q::set("INSERT INTO lib_history(book, user) VALUES (\"".$v."\", \"".$_SESSION["user"]["uid"]."\")");
			if(!$ok) {
				$err = true;
			}
			$updId = code::find($v)["id"];
			$ok = $q::set("UPDATE lib_books SET status = 1 WHERE id = ".$updId);
			if(!$ok) {
				$err = true;
			}
		}
		if($err === true) {
			layout::header();
			echo "Ett fel inträffade. försök gärna igen.";
		} else {
			unset($_SESSION["books"]);
			layout::header();
			echo <<<OUT
Böckerna är nu utlånade<br><a href="index.php">Låna fler</>
OUT;
		}
	} else {
		layout::header();
		echo <<<OUT
<h3 class="errorText">Inga böcker att låna</h3>
OUT;
	}
} else {
	layout::header();
	echo <<<OUT
<h3 class="errorText">Inga böcker att låna</h3>
OUT;
}
layout::footer();
?>