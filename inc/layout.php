<?php
class layout {
	public static function header($url = "") {
		if($url !== "") {
			$url2 = $url;
			$url = "../";
		}
		echo <<<OUT
<html lang="se">
<head>
<meta charset="UTF-8">
<title>Bibliotek</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="{$url}css/css.css">
<script src="{$url}inc/js.js"></script>
<script src="{$url}inc/screensaver.js"></script>
<script src="{$url}inc/draw.js"></script>
<script>
OUT;
		echo("scanUrl = \"".$url.urlencode(conf::get()["config"]["domain"].conf::get()["config"]["scanUrl"])."\";");
		$msg = "";
		$msg = "
window.onload = loaded(
	function(){";
		if(isset($_GET["msg"])) {
			$msg .= "
		popup('";
			if($_GET["msg"] === "1") {
				$msg .= "Du har inte rättigheterna till den sidan!";
			} elseif($_GET["msg"] === "2") {
				$msg .= "Du har angett fel användarnamn/lösenord!";
			} elseif($_GET["msg"] === "3") {
				$msg .= "Något gick fel när informationen skickades från formuläret till databasen. Försök igen!";
			} else {
				$msg .= urldecode($_GET["msg"]);
			}
			$msg .= "');";
		}
		if($_SERVER["SCRIPT_NAME"] === "/index.php") {
			//$msg .= "document.getElementById('scanner').focus();";
		}
		$msg .= "
	}
);";
		echo($msg);
		echo <<<OUT
</script>
</head>
<body onload="init();">
<script>
var s = null;
function init() {
	//s = new Screensaver('screensaver', 1, 2);
	//s.start();
}
</script>
<div id="screensaver">
<canvas style="border: 1pt solid #000; background-color: #000; position: relative; top: 50%; transform: translateY(-50%);"></canvas>
</div>
OUT;
		if(!isset($_SESSION["admin"])) {
			echo <<<OUT
<div id="admin" onclick="adminLogin();"><img src="{$url}img/cog.png" style="width: 16px; height: 16px;"></div>
<div id="adminLoginBox"><p>Logga in</p><form action="{$url}admin.php" method="POST"><input type="text" name="user" placeholder="Användarnamn"><br><input type="password" name="pass" placeholder="Password"><br><input type="submit" value="Logga in"></form></div>
OUT;
		}
		echo <<<OUT
<div id="popup"></div>
<div id="warning">
<p>Text</p>
</div>
<div id="header">
<img src="{$url}img/banner.png">
</div>
<div id="menu">
OUT;
		if($_SERVER["SCRIPT_NAME"] === conf::get()["config"]["folder"]."/index.php") {
			$curr["index"] = " class=\"menuCurrent\"";
			$curr["search"] = "";
			$curr["user"] = "";
			$curr["admin"] = "";
		} elseif($_SERVER["SCRIPT_NAME"] === conf::get()["config"]["folder"]."/search.php") {
			$curr["index"] = "";
			$curr["search"] = " class=\"menuCurrent\"";
			$curr["user"] = "";
			$curr["admin"] = "";
		} elseif($_SERVER["SCRIPT_NAME"] === conf::get()["config"]["folder"]."/user.php") {
			$curr["index"] = "";
			$curr["search"] = "";
			$curr["user"] = " class=\"menuCurrent\"";
			$curr["admin"] = "";
		} elseif($_SERVER["SCRIPT_NAME"] === conf::get()["config"]["folder"]."/admin.php") {
			$curr["index"] = "";
			$curr["search"] = "";
			$curr["user"] = "";
			$curr["admin"] = " class=\"menuCurrent\"";
		} else {
			$curr["index"] = "";
			$curr["search"] = "";
			$curr["user"] = "";
			$curr["admin"] = "";
		}
		if(isset($_SESSION["user"])) {
			echo <<<OUT
<a href="{$url}index.php"{$curr["index"]}>Låna</a>
<a href="{$url}search.php"{$curr["search"]}>Sök</a>
<a href="{$url}user.php"{$curr["user"]}>Jag</a>
<a href="{$url}logout.php">Logga ut</a>
OUT;
		} else {
			echo <<<OUT
		<a href="{$url}index.php"{$curr["index"]}>Hem</a>
	<a href="{$url}search.php"{$curr["search"]}>Sök</a>
OUT;
			if(isset($_SESSION["admin"])) {
				echo <<<OUT

<a href="{$url}logout.php">Logga ut</a>
OUT;
			}
		}
		if(isset($_SESSION["admin"])) {
			echo <<<OUT

<a href="{$url}admin.php"{$curr["admin"]}>Admin</a>
OUT;
		}
		echo <<<OUT
</div>
OUT;
		if(isset($_SESSION["user"])) {
			if(isset($_SESSION["books"])) {
				echo <<<OUT
<div id="bookAddList">
OUT;
				include("{$url}bookaddlist.php");
				echo "</div>";
			}
		}
		echo "<div id=\"content\">";
	}
	public static function footer() {
		$year = date("Y");
		echo <<<OUT
</div>
<div id="footer"><p>Copyright &copy; {$year} IT-Gymnasiet Kristianstad</p></div>
</body>
</html>
OUT;
	}
}
?>