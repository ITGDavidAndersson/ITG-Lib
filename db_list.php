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

$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$tables = ["users", "books"];
if(in_array($_GET["t"], $tables)) {
	
	$r = [];
	$r["users"] = [];
	$r["books"] = [];
	$r["books"]["status"]["0"] = "<img src=\"img/icon_book_in.svg\">";
	$r["books"]["status"]["1"] = "<img src=\"img/icon_book_out.svg\">";
	
	if($_GET["t"] === "books") {
		$group = " GROUP BY title";
	} else {
		$group = "";
	}
	if($_GET["t"] === "users") {
		$q2 = "SELECT COUNT(DISTINCT name) AS c FROM lib_".$_GET["t"].";";
	} else {
		$q2 = "SELECT COUNT(DISTINCT title) AS c FROM lib_".$_GET["t"].";";
	}
	$rows = $sql::get($q2)[0]["c"];
	if(isset($_GET["p"])) {
		$page = $_GET["p"];
	} else {
		$page = 1;
	}
	if(isset($_GET["d"])) {
		$limit = $_GET["d"];
	} else {
		$limit = 25;
	}
	$typesTemp = $sql::get("SELECT DATA_TYPE as T,COLUMN_NAME as C FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'lib_".$_GET["t"]."';");
	$types = [];
	foreach($typesTemp as $k => $v) {
		$types[$v["C"]] = $v["T"];
	}
?>
<table>
<?php
if($limit !== "no") {
	$limitstring = " LIMIT ".($limit*($page-1)).",".$limit;
} else {
	$limitstring = "";
}
$q = "SELECT * FROM lib_".$_GET["t"].$group.$limitstring.";";
$res = $sql::get($q);
if($limitstring !== "") {
?>
<tr><td>
<select onchange="{window.location = 'db_list.php?t=<?php
echo($_GET["t"]."&p=1&d=");
?>'+this.value;}">
<?php
if($_GET["d"] === "10") {
	$def1 = " selected disabled";
	$def2 = "";
	$def3 = "";
	$def4 = "";
} elseif($_GET["d"] === "25") {
	$def1 = "";
	$def2 = " selected disabled";
	$def3 = "";
	$def4 = "";
} elseif($_GET["d"] === "100") {
	$def1 = "";
	$def2 = "";
	$def3 = " selected disabled";
	$def4 = "";
} elseif($_GET["d"] === "no") {
	$def1 = "";
	$def2 = "";
	$def3 = "";
	$def4 = " selected disabled";
} else {
	$def1 = "";
	$def2 = "";
	$def3 = "";
	$def4 = "";
}
echo("<option value='10'".$def1.">10</option>
<option value='25'".$def2.">25</option>
<option value='100'".$def3.">100</option>
<option value='no'".$def4.">Allt</option>
");
?>
</select>
<?php
		echo("Sida: <select onchange=\"{window.location = 'db_list.php?t=".$_GET["t"]."&p='+this.value+'&d=".$limit."';};\">");
		for($c = 1; $c < floor($rows/$limit)+1; $c++) {
			if($c == $page) {
				$sel = " selected disabled";
			} else {
				$sel = "";
			}
			echo("<option value=\"".$c."\"".$sel.">".$c."</option>");
			//echo(" <a href=\"db_list.php?t=".$_GET["t"]."&p=".$c."&d=".$limit."\"".$bold.">&nbsp;".$c."&nbsp;</a> ");
		}
		echo("</td></tr>");
	}
	echo("<tr>");
	foreach($res as $k => $v) {
		if($k !== "id") {
			echo("<th>".$k."</th>");
		}
	}
	echo("<th>Hantera</th></tr>");
	foreach($res as $k => $v) {
		echo("<tr>");
		foreach($v as $k2 => $v2) {
			if($k2 !== "id") {
				$lim = 13;
				if(strlen($v2) > $lim) {
					$val = trim(mb_substr($v2, 0, $lim))."...";
				} else {
					$val = $v2;
				}
				if(isset($r[$_GET["t"]][$k2][$v2])) {
					$val = $r[$_GET["t"]][$k2][$v2];
				}
				echo("<td>".$val."</td>");
			}
		}
		echo("<td><a href=\"db_edit.php?t=".$_GET["t"]."&id=".$v["id"]."\">Ändra</a></td>");
		echo("</tr>");
	}
} else {
	echo("<tr><td>");
}
?>
</table>
<?php
layout::footer();
?>