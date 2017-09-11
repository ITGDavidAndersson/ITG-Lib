<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
layout::header();
if(!isset($_SESSION["books"])) {
	$_SESSION["books"] = [];
}
?>
<form action="search.php" method="GET">
<p style="font-weight: bold;">Söktext <input type="text" name="q"></p><input type="submit" value="sök">
</form>
<script>
function openResults(row) {
	var txt = [];
	txt[1] = [];
	txt[2] = [];
	txt[1][0] = "Visa fler";
	txt[1][1] = "Visa färre";
	txt[2][0] = "Visa ännu fler";
	txt[2][1] = "Visa färre";
	var ti = "";
	if(row === 2) {
		ti = 2;
	} else {
		ti = 1;
	}
	if(document.getElementById('openResults'+row).style.display !== 'block') {
		document.getElementById('openResults'+row).style.display = 'block';
		document.getElementById('openResultsLink'+ti).innerHTML = txt[ti][1];
	} else {
		document.getElementById('openResults'+row).style.display = 'none';
		document.getElementById('openResultsLink'+ti).innerHTML = txt[ti][0];
	}
}
</script>
<?php
if(isset($_GET["q"])) {
	if($_GET["q"] !== "") {
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$query = "SELECT title,subtitle,isbn,status FROM lib_books GROUP BY isbn ORDER by title";
		$res = $sql::get($query);
		if($res !== false) {
			$tmp = [];
			if($_GET["q"] !== "*") {
				foreach($res as $k => $v) {
					$tmp[$k] = (substr_count(strtolower($v["title"]), strtolower($_GET["q"]))*(strlen($_GET["q"])*15))+(strlen($_GET["q"])-levenshtein(strtolower($_GET["q"]), strtolower($v["title"])));
				}
				arsort($tmp);
			} else {
				$c = 0;
				$lvl = 100;
				foreach($res as $k => $v) {
					if($c === 10) {
						$lvl = 60;
					} elseif($c == 20) {
						$lvl = 30;
					} elseif($c == 30) {
						$lvl = 0;
					}
					$tmp[$k] = $lvl;
					$c++;
				}
			}
			$switch = 0;
			echo("<div>");
			$c = 0;
			foreach($tmp as $k => $v) {
				if($v > 20) {
					if($res[$k]["subtitle"] !== "") {
						$subt = " - ".$res[$k]["subtitle"];
					} else {
						$subt = "";
					}
					if($c === 10) {
						echo "</div><br><a href=\"#\" onclick=\"openResults('');\" style=\"margin-left: 10pt;\" id=\"openResultsLink1\">Visa fler</a><br><div style=\"display: none; margin: 5pt 0pt;\" id=\"openResults\">";
						$switch = 1;
					} elseif($c == 20) {
						echo "<br><a href=\"#\" onclick=\"openResults(2);\" style=\"margin-left: 10pt;\" id=\"openResultsLink2\">Visa ännu fler</a></div><div style=\"display: none; margin: 5pt 0pt;\" id=\"openResults2\">";
						$switch = 2;
					}
					if($res[$k]["status"] === "1") {
						$status = "<span style=\"color: #f00;\">X</span>";
					} else {
						$status = "<span style=\"color: #0f0;\">O</span>";
					}
					echo $status." <a href='book.php?return=".$_GET["q"]."&isbn=".$res[$k]["isbn"]."'>".$res[$k]["title"].$subt."</a><br>";
				}
				$c++;
			}
			if($switch !== 0) {
				echo("</div>");
			}
		}
	}
}
layout::footer();
?>