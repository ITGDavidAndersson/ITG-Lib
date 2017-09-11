<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/books.php");
layout::header();
$b = [];
if(isset($_SESSION["books"])) {
	if(count($_SESSION["books"]) === 0) {
		echo <<<OUT
<h3>Inga böcker att låna</h3>
OUT;
	} else {
		echo <<<OUT
<h1>Böcker att låna</h1>
OUT;
		foreach($_SESSION["books"] as $v) {
			$b = new books(code::find($v)["isbn"]);
			$book = $b->get();
			echo "<div style=\"display: table-row;\"><div style=\"display: table-cell;\"><p>".$book["title"]."</p></div><div style=\"display: table-cell; padding-left: 4pt;\"><a href='del.php?c=".$v."'>Ta bort</a></div></div>";
			unset($b);
		}
		echo <<<OUT
<hr>
<a href="confirm.php">Godkänn lån</a>
OUT;
	}
} else {
	echo <<<OUT
<p>Du har inte lagt till några böcker</p>
OUT;
}
layout::footer();
?>