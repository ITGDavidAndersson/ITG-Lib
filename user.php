<?php
session_name("ITGlib");
session_start();
if(!isset($_SESSION["user"])) {
	header("Location: index.php");
}
require("inc/base.php");
require("inc/books.php");
require("inc/timeConverter.php");
layout::header();
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$res = $sql::get("SELECT name,address,mphone,hphone,mail FROM lib_users WHERE uid = '".$_SESSION["user"]["uid"]."'");
if($res !== false) {
	echo <<<OUT
<h3>Information</h3>
<table class="contactDetails">
<tr><td><p>Namn</p></td><td><p>{$res[0]["name"]}</p></td></tr>
<tr><td><p>Adress</p></td><td><p>{$res[0]["address"]}</p></td></tr>
<tr><td><p>E-Post</p></td><td><p>{$res[0]["mail"]}</p></td></tr>
<tr><td><p>Mobilnummer</p></td><td><p>{$res[0]["mphone"]}</p></td></tr>
<tr><td><p>Hemtelefon</p></td><td><p>{$res[0]["hphone"]}</p></td></tr>
</table><br>
OUT;
} else {
	echo("Inga uppgifter finns...");
}
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$res = $sql::get("SELECT book,time FROM lib_history WHERE user = '".$_SESSION["user"]["uid"]."' AND returned IS NULL ORDER BY time ASC");
if($res !== false) {
	echo("<table class=\"bookList\"><tr><th><p>Bok</p></th><th><p>Tid kvar</p></th></tr>");
	$mt = INF;
	foreach($res as $k => $v) {
		$tl = strtotime("+".conf::get()["config"]["borrowTime"][$_SESSION["user"]["type"]]." days", strtotime($v["time"]))-(time()+(60*60));
		if($mt > $tl) {
			$mt = $tl;
		}
		$b = code::find($v["book"]);
		if($tl > 0) {
			$warningType = "";
			if($tl <= 60*60*6) {
				$warningType = " <p style=\"display: inline-block; width: 6pt; height: 6pt; background: #f00; border-radius: 2pt; vertical-align: middle;\"></p>";
			} elseif($tl <= 60*60*24) {
				$warningType = " <p style=\"display: inline-block; width: 6pt; height: 6pt; background: #f80; border-radius: 2pt; vertical-align: middle;\"></p>";
			} elseif($tl <= 60*60*24*4) {
				$warningType = " <p style=\"display: inline-block; width: 6pt; height: 6pt; background: #ff0; border-radius: 2pt; vertical-align: middle;\"></p>";
			}
			$ts = timeConverter::timeSince($tl, "short");
			echo("<tr><td><a href=\"book.php?isbn=".$b["isbn"]."\">".$b["title"]."</a></td><td><p style=\"display: inline;\">".$ts." kvar</p>".$warningType."</td></tr>");
		} elseif($tl < 0) {
			echo("<tr class=\"bookLate\"><td><p>".$b["title"]."</p></td><td><p>Försenad!</p></td></tr>");
		}
	}
	echo("</table>");
	if($mt > 0) {
		if($mt <= 60*60) {
			echo("<script>
document.onload = warningTimer(".$mt.");
</script>");
		}
	}
}
layout::footer();
?>