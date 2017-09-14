<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/books.php");
require("inc/cats.php");
layout::header();
if(isset($_GET["return"])) {
	echo("<a href=\"search.php?q=".$_GET["return"]."\"><- Sökresultat</a>");
}
$b = new books($_GET["isbn"]);
$book = $b->get();
/*if($book["status"] === "1") {
	$status = "<span style=\"color: #f00;\">X</span>";
} else {
	$status = "<span style=\"color: #0f0;\">O</span>";
}*/
echo "<p style=\"font-style: italic;\">".$book["author"]."</p>
<h1>".$book["title"]."</h1>");// ".$status."</h1>";
$list = [$book["subtitle"], $book["lang"], cats::get($book["cat"]), $book["info"], $book["img"]];
$status = "";
$txt = [
	"<h3 style=\"font-style: italic;\">".$book["subtitle"]."</h3>",
	"<p style=\"font-style: italic;\">".$book["lang"]."</p>",
	"<p style=\"font-style: italic;\">".cats::get($book["cat"])."</p>",
	"<p style=\"float: left; margin: 5pt; border: 1pt solid #aaa; padding: 5pt;\">".$book["info"]."</p>",
	"<img src=\"".$book["img"]."\" style=\"margin: 5pt;\">"
];
foreach($list as $k => $v) {
	if($v !== "") {
		echo($txt[$k]);
	}
}
echo("<br>");//Antal: ".$book["number"]."<br>");
if(isset($_SESSION["admin"])) {
	echo("<a href=\"printbooks.php?a=".$book["isbn"]."\">Skriv ut streckkod för denna boken</a><br><a href=\"admineditbook.php?id=".$book["isbn"]."\">Redigera boken</a>");
}
layout::footer();
?>