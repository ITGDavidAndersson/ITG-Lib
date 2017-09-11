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
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$keys = [
	"id" => "h",
	"isbn" => "i",
	"title" => "i",
	"cat" => "i",
	"subtitle" => "i",
	"author" => "i",
	"lang" => "i",
	"info" => "t",
	"img" => "m"
];
$query = "UPDATE lib_books SET ";
if(isset($_POST["isbn"])) {
	foreach($keys as $k => $v) {
		if($k !== "id") {
			if($query === "UPDATE lib_books SET ") {
				$query .= $k." = '".$_POST[$k]."'";
			} else {
				$query .= ", ".$k." = '".$_POST[$k]."'";
			}
		}
	}
	$query .= " WHERE id = ".$_POST["id"];
	$ok = $sql::set($query);
	if($ok) {
		header("Location: book.php?isbn=".$_POST["isbn"]);
	} else {
		print_r($ok);
	}
}
require("inc/books.php");
layout::header();
$b = new books($_GET["id"]);
$b = $b->get();
?>
<h3>Ändra bok</h3>
<p><i><?php echo($b["title"]); ?></i></p>
<form action="admineditbook.php" method="POST">
	<table>
<?php
foreach($keys as $k => $v) {
	if($k === "cat") {
?>
<tr>
	<th>
		<p>Kategori</p>
	</th>
	<td>
		<select name="cat">
<?php
	$dbmains = $sql::get("SELECT name,fullname,parent FROM lib_cats WHERE parent IS NULL ORDER BY fullname ASC");
	$dbcats = $sql::get("SELECT name,fullname,parent FROM lib_cats ORDER BY fullname ASC");
	$cats = [];
	$mains = [];
	$subs = [];
	if($dbcats !== false) {
		foreach($dbcats as $k => $v) {
			$cats[$v["name"]] = $v;
			if($v["parent"] !== null) {
				if(!isset($subs[$v["parent"]])) {
					$subs[$v["parent"]] = [];
				}
				array_push($subs[$v["parent"]], $v);
			} else {
				$mains[$v["name"]] = $v;
			}
		}
	}
	if(count($cats) !== 0) {
		foreach($mains as $k => $v) {
			echo("<option disabled>".str_repeat("- ", $cats[$v["name"]]["lvl"]).ucfirst($cats[$v["name"]]["fullname"])."</option>");
			foreach($subs[$k] as $k2 => $v2) {
				$tcat = "";
				if($v2["name"] == substr($b["cat"], 2, 2)) {
					$tcat = " selected";
				}
				echo("<option value=\"".$cats[$v2["name"]]["parent"].$cats[$v2["name"]]["name"]."\"".$tcat."> - ".ucfirst($cats[$v2["name"]]["fullname"])."</option>");
			}
		}
	} else {
		echo("<option value=\"null\">* FEL *</option>");
	}
?>
</select></td></tr>
<?php
	} else {
		if($v === "i") {
			$inp = "<input type=\"text\" name=\"".$k."\" value=\"".$b[$k]."\">";
		} elseif($v === "h") {
			$inp = "<input type=\"hidden\" name=\"".$k."\" value=\"".$b[$k]."\">";
		} elseif($v === "t") {
			$inp = "<textarea name=\"".$k."\" style=\"width: 80vw; height: 15vh;\">".$b[$k]."</textarea>";
		} elseif($v === "m") {
			$inp = "<input type=\"text\" name=\"".$k."\" value=\"".$b[$k]."\" id=\"url\" oninput=\"{document.getElementById('img').src = this.value;};\" style=\"width: 80vw;\"><a href=\"#\" onclick=\"{document.getElementById('img').src = '".$b[$k]."';	document.getElementById('url').value = '".$b[$k]."';};\">Återställ</a><br><img id=\"img\" src=\"".$b[$k]."\">";
		}
		if($v !== "h") {
			echo("<tr><th><p>".$k."</p></th><td>".$inp."</td></tr>");
		} else {
			echo($inp);
		}
	}
}
?>
<tr><td></td><td><input type="submit" value="Spara"></td></tr></table></form></div>
<?php
layout::footer();
?>