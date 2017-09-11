<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/books.php");
if(!isset($_SESSION["admin"])) {
	header("Location: index.php?msg=1");
}
if(!isset($_GET["a"])) {
	header("Location: index.php");
} else {
	$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
	if($_GET["a"] === "return") {
		if(isset($_GET["id"])) {
			$ok = $sql::set("UPDATE lib_history SET returned = NOW() WHERE id = ".$_GET["id"].";");
			if($ok) {
				header("Location: temp.php?a=return&msg=".urlencode("Boken har lämnats tillbaka"));
			} else {
				header("Location: temp.php?a=return&msg=".urlencode("Boken kunde inte återlämnas. Försök igen eller kontakta David."));
			}
		}
	} elseif($_GET["a"] === "activate") {
		if(isset($_POST["id"])) {
			$ok = $sql::get("SELECT * FROM lib_users WHERE name = \"".trim($_POST["targetId"])."\";");
			$userId = $ok[0]["id"];
			if($ok) {
				$ok = $sql::set("UPDATE lib_tusers SET active = 1, con = ".$userId.", namn = \"".$_POST["targetId"]."\" WHERE id = ".$_POST["id"].";");
			} elseif($_POST["targetId"] != "") {
				$ok = $sql::set("UPDATE lib_tusers SET active = 1 AND namn = \"".$_POST["targetId"]."\" WHERE id = ".$_POST["id"].";");
			}
			if($ok) {
				header("Location: temp.php?a=activate&msg=".urlencode("Kontot har aktiverats"));
			} else {
				header("Location: temp.php?a=activate&msg=".urlencode("Kontot kunde inte aktiveras. Försök igen eller kontakta David."));
			}
		}
	}
}
layout::header();
if(isset($_GET["a"])) {
	if($_GET["a"] === "return") {
		echo <<<out
<h2>Lämna tillbaka böcker</h2>
out;
		$books = $sql::get("SELECT id,book,user,time FROM lib_history WHERE returned IS NULL;");
		$usersdb = $sql::get("SELECT id,namn FROM lib_tusers;");
		$users = [];
		foreach($usersdb as $v) {
			$users[$v["id"]] = $v["namn"];
		}
		echo("<table><tbody><tr><th><p>Bok</p></th><th><p>Lånare</p></th><th colspan=2><p>Lånad</p></th></tr>");
		foreach($books as $v) {
			echo("<tr><td><p>".$v["book"]."</p></td><td><p>".$users[$v["user"]]."</p></td><td><p>".date("d/m-y", strtotime($v["time"]))."</p></td><td><a href=\"temp.php?a=return&id=".$v["id"]."\">Lämna</a></tr>");
		}
		echo("</tbody></table>");
	} elseif($_GET["a"] === "activate") {
		echo <<<out
<h2>Aktivera konton</h2>
out;
		$ok = $sql::get("SELECT COUNT(*) as c FROM lib_tusers WHERE active = 0;");
		if($ok[0]["c"] != 0) {
			echo <<<out
<script>
function updActivate() {
	var uid = document.getElementById("uid").value;
}
var unames = [
</script>
<form action="temp.php?a=activate" method="POST">
<select name="id" id="uid" onchange="updActivate();">
out;
			$usersdb = $sql::get("SELECT id,namn,user FROM lib_tusers WHERE active = 0 ORDER BY namn ASC;");
			foreach($usersdb as $v) {
				echo("<option value=\"".$v["id"]."\">".$v["user"]." - ".$v["namn"]."</option>");
			}
			echo("</select><input name=\"targetId\" list=\"tId\" id=\"targetId\"><datalist id=\"tId\">");
			$usersdb = $sql::get("SELECT id,name FROM lib_users ORDER BY name ASC;");
			foreach($usersdb as $v) {
				echo("<option value=\"".$v["name"]."\">".$v["id"]."</option>");
			}
			echo <<<out
</datalist>
<input type="submit" value="Aktivera användare">
</form>
out;
		} else {
			echo "<p>Inga konton att aktivera</p>";
		}
	}
}
layout::footer();
?>