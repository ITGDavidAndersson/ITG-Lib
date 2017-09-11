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
$tables = ["users", "books"];
if(in_array($_GET["t"], $tables)) {
	$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
	$typesTemp = $sql::get("SELECT DATA_TYPE as T,COLUMN_NAME as C FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'lib_".$_GET["t"]."';");
	$types = [];
	foreach($typesTemp as $k => $v) {
		$types[$v["C"]] = $v["T"];
	}
	$q = "UPDATE lib_".$_GET["t"]." SET ";
	$ok = false;
	foreach($_POST as $k => $v) {
		if($ok === true) {
			$q .= ", ";
		} else {
			$ok = true;
		}
		if($types[$k] === "int") {
			$char = "";
		} else {
			$char = "'";
		}
		$q .= $k." = ".$char.$v.$char;
	};
	$q .= " WHERE id = ".$_GET["id"];
	$ok = $sql::set($q);
	if(!$ok) {
		if(!isset($_SESSION["msg"])) {
			$_SESSION["msg"] = [];
		}
		array_push($_SESSION["msg"], "Det gick inte att ändra i databasen. Försök igen.");
	}
	header("Location: db_edit.php?t=".$_GET["t"]."&id=".$_GET["id"]);
}