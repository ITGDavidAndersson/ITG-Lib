<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/ad.php");
if($_GET["t"] === "scan") {
	echo <<<OUT
<form action="login.php" method="POST"><input type="hidden" id="codeEntry" name="id" value="
OUT;
echo $_GET["c"];
echo <<<OUT
"><input type="password" name="pass" placeholder="Din personliga kod"><input type="submit" value="Logga in"><form>
OUT;
} else {
	if(!isset($_SESSION["user"])) {
		$username = $_POST["id"];
		$password = $_POST["pass"];
		$ad = new ad($username, $password);
		if($ad->status !== "connected") {
			echo "ERROR CONNECTING<br>";
		}
		if(isset($_POST["user"])) {
			$_SESSION["user"] = ["uid" => $username, "type" => 1];
			header("Location: index.php");
		} else {
			header("Location: index.php?msg=".urlencode($ad->status)); //err=login&
		}
		
		
		/*$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$res = $sql::get("SELECT uid,pass,type FROM lib_users WHERE uid = '".$_POST["id"]."' AND pass = '".sha1($_POST["pass"])."'");
		if(count($res) === 1) {
			if(($res[0]["uid"] === $_POST["id"]) && ($res[0]["pass"] === sha1($_POST["pass"]))) {
				$_SESSION["user"] = ["uid" => $res[0]["uid"], "type" => $res[0]["type"]];
				header("Location: index.php");
			} else {
				header("Location: index.php?err=hacker");
			}
		} else {
			header("Location: index.php?err=login");
		}*/
	} else {
		header("Location: index.php?err=alreadyLogged");
	}
}
?>