<?php
session_name("ITGlib");
session_start();
if(!isset($_SESSION["admin"])) {
	header("Location: index.php?msg=1");
} 
$posts = ["isbn", "name", "subtitle", "author", "antal", "lang", "desc", "img", "allinfo", "cat"];
$keys = ["isbn", "title", "subtitle", "author", "lang", "info", "img", "allinfo", "cat", "catindex", "bindex"];
$types = ["string", "string", "string", "string", "string", "string", "string", "int", "string", "int", "int"];
$vals = [];
//		Check all posts if they exist
foreach($posts as $v) {
	if(!isset($_POST[$v])) {
		header("Location: admin.php?msg=".urlencode($v));
	} else {
		$vals[$v] = addslashes($_POST[$v]);
	}
}
require("inc/base.php");
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
//		Check to see if subcategory exists. Otherwise, create it
if(isset($_POST["newcat"])) {
	if($_POST["newcat"] !== "") {
		$newcat = strtolower(str_replace(["å", "ä", "ö"], ["a", "a", "o"], $_POST["newcat"]));
		for($c = 0; $c < strlen($newcat); $c++) {
			$try = $sql::get("SELECT COUNT(*) as c FROM lib_cats WHERE name = '".substr($newcat, $c, 2)."'");
			if($try[0]["c"] == 0) {
				$newname = substr($newcat, $c, 2);
				break;
			}
		}
		if(!isset($newname)) {
			header("Location: admin.php?msg=".urlencode("Kunde inte skapa ny kategori. Testa med ett annat namn på kategorin"));
			break;
		} else {
			$ok = $sql::set("INSERT INTO lib_cats(name,fullname,parent) VALUES('".$newname."', '".ucfirst($_POST["newcat"])."', '".$vals["cat"]."')");
			if($ok) {
				$vals["cat"] = $newname;
			} else {
				header("Location: admin.php?msg=".urlencode("Kunde inte skapa kategorin. Försök igen. Fungerar det inte så kontakta David tillsammans med eventuellt felmeddelande."));
			}
		}
	}
}
//		Add parent category to book
$res = $sql::get("SELECT * FROM lib_cats WHERE name = '".$vals["cat"]."'");
if($res !== false) {
	$vals["cat"] = strtolower($res[0]["parent"].$vals["cat"]);
} else {
	header("Location: admin.php?msg=".urlencode("Det blev ett fel med kategorierna i databasen. Existerar kategorin? Du behöver möjligtvis kontakta David."));
}
//		Fetch index for this book in the category
$res = $sql::get("SELECT catindex AS c FROM lib_books WHERE isbn = '".$vals["isbn"]."'");
if($res) {
	$vals["catindex"] = intval($res[0]["c"]);
} else {
	$res = $sql::get("SELECT MAX(catindex) AS c FROM lib_books WHERE cat = '".$vals["cat"]."'");
	if(intval($res[0]["c"]) === 0) {
		$vals["catindex"] = intval($res[0]["c"]);
	} else {
		$vals["catindex"] = intval($res[0]["c"])+1;
	}
}
//		Fetch index for where to start adding more of this exact book
$res = $sql::get("SELECT COUNT(*) AS c FROM lib_books WHERE isbn = '".$vals["isbn"]."'");
$bindex = intval($res[0]["c"]);
//		Insert new book into database
$numberOfBooks = $vals["antal"];
unset($vals["antal"]);
$c = 0;
foreach($vals as $k => $v) {
	if($types[$c] === "string") {
		$vals[$k] = "'".$v."'";
	} else {
		$vals[$k] = intval($v);
	}
	$c++;
}
$fok = true;
$vals["allinfo"] = "\"".urlencode($vals["allinfo"])."\"";
for($c = 0; $c < $numberOfBooks; $c++) {
	$vals["bindex"] = $bindex+$c;
	$pvals = implode(",", $vals);
	$pkeys = implode(",", $keys);
	$ok = $sql::set("INSERT INTO lib_books(".$pkeys.") VALUES(".$pvals.")");
	if(!$ok) {
		$fok = $ok;
	}
}
if($fok) {
	header("Location: admin.php?msg=".urlencode("Boken har lagts till."));
} else {
	header("Location: admin.php?msg=".urlencode("Boken kunde inte läggas till. Felmeddelande: ".$fok));
}
?>