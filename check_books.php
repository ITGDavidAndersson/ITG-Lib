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
require("inc/books.php");
layout::header();
?>
<script>
var timer = 0;
var str = "";
Element.prototype.documentOffsetTop = function() {
    return this.offsetTop+(this.offsetParent?this.offsetParent.documentOffsetTop():0);
};
function upd(val) {
	if(timer != null) {
		clearTimeout(timer);
		timer = setTimeout(upd2, 200);
	} else {
		for(var c = 0; c < document.getElementById("tabba").children[0].children.length; c++) {
			if(document.getElementById("tabba").children[0].children[c].children[1].innerHTML == val) {
				for(var q = 0; q < document.getElementById("tabba").children[0].children[c].children.length; q++) {
					var el = document.getElementById("tabba").children[0].children[c].children[q];
					el.style.backgroundColor = "#0f0";
					var top = el.documentOffsetTop()-(window.innerHeight/2);
					cols[c] = true;
					//window.scrollTo(0, top);
				}
			}
		}
		timer = 0;
		document.getElementById("inpupd").value = "";
		updMini();
	}
}
function upd2() {
	timer = null;
	upd(document.getElementById("inpupd").value);
}
addEventListener("load", function() {
	document.getElementById("shade").style.display = "block";
	document.getElementById("shade2").style.display = "none";
	document.getElementById("inpupd").focus();
	document.getElementById("inpupd").addEventListener("blur", function() {
		document.getElementById("inpupd").focus();
	});
});
function updMini() {
	var q = 0;
	for(var c = 0; c < document.getElementById("tabba").children[0].children.length; c++) {
		if(cols[c] == true) {
			document.getElementById("mini").children[c+1].style.backgroundColor = "#0f0";
			q++;
		} else {
			document.getElementById("mini").children[c+1].style.backgroundColor = "#f00";
		}
	}
	document.getElementById("mini").children[0].innerHTML = q+" böcker skannade";
}
<?php
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$q2 = "SELECT * FROM lib_books ORDER BY title;";
$rows = $sql::get($q2);
?>
var cols = [
<?php
$c = 0;
foreach($rows as $v) {
	if($c == 0) {
		echo("false");
	} else {
		echo(",
false");
	}
	$c++;
}
?>
];
var cleared = false;
function clearBooks() {
	for(var c = 0; c < document.getElementById("tabba").children[0].children.length; c++) {
		var el = document.getElementById("tabba").children[0].children[c];
		if(cols[c] == true) {
			if(cleared == false) {
				el.style.display = "none";
				cleared = true;
			} else {
				el.style.display = "table-row";
				cleared = false;
			}
		}
	}
}
</script>
<div id="mini" style="width: 500pt;">
<p>0 böcker scannade</p>
<?php
foreach($rows as $v) {
	echo("<div style=\"width: 5px; height: 5px; background-color: #f00; border: 1px solid #000; display: inline-block;\"></div>");
}
?>
</div>
<input type="text" id="inpupd" oninput="upd(this.value)" style="width: 1pt;"><input type="button" onclick="clearBooks();" value="Filtrera skannade böcker">
<div id="shade" style="display: none;">
<?php
echo("<table id=\"tabba\">");
foreach($rows as $v) {
	echo("<tr><td>".$v["title"]."</td><td>".code::get($v)."</td><td><img src=\"barcode.php?text=".code::get($v)."&size=40\"></td></tr>");
}
echo("</table>
</div><div id=\"shade2\">Laddar</div>");
layout::footer();
?>