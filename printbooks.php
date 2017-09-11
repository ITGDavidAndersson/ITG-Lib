<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@media print{
	div{
		page-break-inside: avoid;
	}
}
</style>
</head>
<body onload="print();">
<?php
require("inc/base.php");
require("inc/cats.php");
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$cats = $sql::get("SELECT * FROM lib_cats");
if($_GET["a"] === "all") {
	$res = $sql::get("SELECT * FROM lib_books ORDER BY cat DESC");
} else {
	$res = $sql::get("SELECT * FROM lib_books WHERE isbn = '".$_GET["a"]."'");
}
$cat = "";
foreach($res as $v) {
	if($v["cat"] !== $cat) {
		if($cat !== "") {
			echo("</div>");
		}
		echo("<div style=\"float: left; border: 1px solid #000; padding: 10px;\"><p>".cats::get($v["cat"])."</p>");
	}
	echo("<div style=\"text-align: center; float: left; border: 1px solid #000; margin: 10px; padding: 0px 5px; max-width: 6cm;\"><p style=\"line-height: 0pt;\">".$v["title"]."</p><p style=\"line-height: 0pt; font-style: italic;\">".$v["subtitle"]."</p><img src=\"barcode.php?text=".code::get($v)."&size=40\"><p style=\"line-height: 0pt;\">".code::get($v)."</p></div>");	$cat = $v["cat"];
}
?>
</div>
</body>
</html>