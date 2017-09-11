<?php
session_name("ITGlib");
session_start();
if(!isset($_SESSION["admin"])) {
	if(isset($_POST["user"]) && isset($_POST["pass"])) {
		if(($_POST["user"] === "") || ($_POST["user"] === "")) {
			header("Location: index.php?msg=2");
		} else {
			if(($_POST["user"] === "admin") && ($_POST["pass"] === "1234")) {
				$_SESSION["admin"] = true;
			} else {
				header("Location: index.php?msg=2");
			}
		}
	} else {
		header("Location: index.php?msg=1");
	}
} 
require("inc/base.php");
layout::header();
?>
<form action="db_edit_save.php?t=<?php echo($_GET["t"]."&id=".$_GET["id"]); ?>" method="POST">
<table>
<?php
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$tables = ["users", "books"];
echo("<tr><td colspan=2><a href=\"db_list.php?t=".$_GET["t"]."\"><-- Tillbaka</a></td></tr>");
if(in_array($_GET["t"], $tables)) {
	$q = "SELECT * FROM lib_".$_GET["t"]." WHERE id=".$_GET["id"].";";
	$res = $sql::get($q);
	foreach($res as $k => $v) {
		foreach($v as $k2 => $v2) {
			if($k2 !== "id") {
				if(strlen($v2) > 25) {
					$inp = "<textarea name=\"".$k2."\">".$v2."</textarea>";
				} else {
					$inp = "<input type=\"text\" name=\"".$k2."\" value=\"".$v2."\">";
				}
				echo("<tr><th>".$k2."</th><td>".$inp."</td></tr>");
			}
		}
	}
	echo("<tr><td colspan=2><input type=\"submit\" value=\"Spara\"></td></tr>");
}
?>
</table>
</form>
<?php
layout::footer();
?>