<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/books.php");
if(!isset($_SESSION["admin"])) {
	$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
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
layout::header();
$isbn = "";
?>
<script>
function admScan() {
	if(scanUrl != null) {
		var ok = window.open("http://zxing.appspot.com/scan?ret=http%3A%2F%2F<?php
		echo(urlencode(conf::get()["config"]["domain"]."/admin.php"));
		?>%3Fc%3D%7BCODE%7D&SCAN_FORMATS=EAN_13", "_self");
		if(ok === null) {
			return false;
		} else {
			return true;
		}
	} else{
		popup("Finns ingen scanUrl definierad. Kontakta David");
		return false;
	}
}
function getInfo(isbn) {
	if(isbn !== "") {
		popup("Hämtar bok...");
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "https://www.googleapis.com/books/v1/volumes?q=isbn:"+isbn+"&key=AIzaSyC3iyKdd0c6zjYc0n0X_rrpF3ohfWZk-z8", true);
		xhr.onreadystatechange = function () {
			var DONE = 4;
			var OK = 200;
			if(xhr.readyState === DONE) {
				if(xhr.status === OK) {
					ret(xhr.response);
				} else {
					ret("Error: "+xhr.status);
				}
			}
		};
		xhr.send(null);
	} else {
		popup("Du måste fylla i ett ISBN-nummer för att kunna hämta en boks information.");
	}
}
function put(txt, id) {
	document.getElementById(id).value = txt;
}
function ret(txt){
	var txt = JSON.parse(txt);
	/*var t = "";
	for(var c in txt) {
		t += c+": "+txt[c]+"\n";
	}*/
	if(txt.totalItems === 0) {
		popup("Hittar inte boken.");
	} else {
		var nrofbooks = txt.totalItems;
		txt = txt.items[0];
		document.getElementById("allinfo").value = JSON.stringify(txt);
		var infoStrings = ["title", "subtitle", "authors", "language", "publishedDate", "description", "imageLinks", "categories"];
		for(var c in infoStrings) {
			if(typeof(txt.volumeInfo[infoStrings[c]]) === "undefined") {
				txt.volumeInfo[infoStrings[c]] = "";
				if(infoStrings[c] === "imageLinks") {
					txt.volumeInfo[infoStrings[c]] = {"thumbnail": false};
				}
			}
		}
		var t = {"title": txt.volumeInfo.title,
		"subtitle": txt.volumeInfo.subtitle,
		"authors": txt.volumeInfo.authors,
		"lang": txt.volumeInfo.language,
		"published": txt.volumeInfo.publishedDate,
		"desc": txt.volumeInfo.description.replace(/↵/g, "\n"),
		"img": txt.volumeInfo.imageLinks.thumbnail,
		"cat": txt.volumeInfo.categories}
		var isbn = document.getElementById("isbn").value;
		document.getElementById("postform").reset();
		put(isbn, "isbn");
		put(t.title, "title");
		put(t.subtitle, "subt");
		put(t.authors, "author");
		put(lang.lookup(t.lang), "lang");
		put(t.desc, "desc");
		if(t.cat !== "") {
			var catPut = false;
			for(var c = 0; c < document.getElementById("cat").children.length; c++) {
				if(document.getElementById("cat").children[c].dataset.name == t.cat.toString().toLowerCase()) {
					document.getElementById("cat").selectedIndex = c;
					catPut = true;
				}
			}
			if(catPut === false) {
				put(t.cat, "newcat");
				document.getElementById("cat").selectedIndex = 0;
			}
		}
		if(t.img !== false) {
			document.getElementById("img").src = t.img;
			document.getElementById("imgurl").value = t.img;
		}
		popup("Information om boken har hämtats! ("+nrofbooks+")");
	}
}
window.onload = function() {
	setTimeout(function() {
		var isbn = document.getElementById("isbn");
		isbn.focus();
		isbn.oninput = function() {
			if((isbn.value.length === 10) || (isbn.value.length === 13)) {
				setTimeout(function(){
					if((isbn.value.length === 10) || (isbn.value.length === 13)) {
						getInfo(isbn.value);
					}
				}, 100);
			}
		};
		<?php
		if(isset($_GET["c"])) {
			echo("document.getElementById(\"isbn\").value = ".$_GET["c"].";
getInfo(".$_GET["c"].");");
		}
		?>
		if((document.getElementById("cat").children[document.getElementById("cat").selectedIndex].innerHTML.substr(0, 1) === " - ") && (document.getElementById("newcat").disabled === false)) {
			document.getElementById('newcat').value = '';
			document.getElementById('newcat').disabled = true;
			document.getElementById('catTxt').innerHTML = 'Kategori';
		}
	}, 1);
};
function checkSubmit() {
	var vars = [
		"title",
		"cat",
		"lang"
	];
	var go = true;
	if(document.getElementById("title").value === "") {
		go = "Du måste fylla i en titel för boken!";
	} else if(document.getElementById("count").value < 1) {
		go = "Du måste fylla i antal böcker!"
	} else if((document.getElementById("cat").children[document.getElementById("cat").selectedIndex].innerHTML.substr(0, 3) !== " - ") && (document.getElementById("newcat").value === "")) {
		go = "Du måste fylla i en underkategori!";
	} else if(document.getElementById("lang").value === "") {
		go = "Du måste fylla i vilket språk boken är på!";
	}
	if(go === true) {
		document.getElementById("postform").submit();
	} else {
		popup(go);
	}
}
</script>
<h3>Administratör</h3>
<div style="float:left;">
<form action="adminaddbook.php" method="POST" id="postform">
<table class="listTable">
<tr><th colspan="2"><p>Lägg till bok</th></tr>
<tr><td><p>ISBN</p></td><td><input type="text" name="isbn" id="isbn" placeholder="ISBN"><input type="button" onclick="admScan();" value="Skanna"><input type="button" value="Hämta info" onclick="getInfo(document.getElementById('isbn').value);"></td><td rowspan=6><img src="" id="img" style="max-height: 120pt;"></td></tr>
<tr><td><p>Titel</p></td><td><input type="text" name="name" id="title" placeholder="Boktitel"></td></tr>
<tr><td><p>Undertitel</p></td><td><input type="text" name="subtitle" id="subt" placeholder="Undertitel"></td></tr>
<tr><td><p>Antal</p></td><td><input type="text" name="antal" id="count" placeholder="Antal" value="1"></td></tr>
<tr><td><p id="catTxt">Kategori<br><i>- Underkat.</i></p></td><td>
	<select name="cat" id="cat" onchange="
		if(this.children[this.selectedIndex].innerHTML.substr(0, 3) === ' - ') {
			document.getElementById('newcat').value = '';
			document.getElementById('newcat').disabled = true;
			document.getElementById('catTxt').innerHTML = 'Kategori';
		} else {
			document.getElementById('newcat').focus();
			document.getElementById('newcat').disabled = false;
			document.getElementById('catTxt').innerHTML = 'Kategori<br><i>- Underkat.</i>';
		}">
<?php
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
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
		echo("<option value=\"".$cats[$v["name"]]["name"]."\" data-name=\"".strtolower($cats[$v["name"]]["fullname"])."\">".str_repeat("- ", $cats[$v["name"]]["lvl"]).ucfirst($cats[$v["name"]]["fullname"])."</option>");
		foreach($subs[$k] as $k2 => $v2) {
			echo("<option value=\"".$cats[$v2["name"]]["name"]."\" data-name=\"".strtolower($cats[$v2["name"]]["fullname"])."\"> - ".ucfirst($cats[$v2["name"]]["fullname"])."</option>");
		}
	}
} else {
	echo("<option value=\"null\">* FEL *</option>");
}
?>
	</select><br>
	<input type="text" name="newcat" id="newcat" placeholder="Underkategori" oninput="if(document.getElementById('cat').children[document.getElementById('cat').selectedIndex].innerHTML.substr(0, 1) === '-') {
		var toChoose = 0;
		for(var c = document.getElementById('cat').selectedIndex; c > 0; c--) {
			if(document.getElementById('cat').children[c].innerHTML.substr(0, 1) !== '-') {
				toChoose = c;
			}
		}
		document.getElementById('cat').selectedIndex = toChoose;
	};
	if(this.value !== ''){
		document.getElementById('catTxt').innerHTML = 'Kategori<br><i>- Underkat.</i>';
	} else {
		document.getElementById('catTxt').innerHTML = 'Kategori';
	};">
</td></tr>
<tr><td><p>Författare</p></td><td><input type="text" name="author" id="author" placeholder="Separera flera med komma"></td></tr>
<tr><td><p>Språk</p></td><td><input type="text" name="lang" id="lang" placeholder="Språk"></td></tr>
<tr><td><p>Beskrivning</p></td><td colspan=2><textarea name="desc" id="desc" style="min-width: 40vw; min-height: 20vh;" placeholder="Beskrivning av boken"></textarea></td></tr>
<tr><td></td><td colspan=2><input type="button" value="Lägg till" onclick="checkSubmit();"></td></tr>
</table>
<input type="hidden" name="img" id="imgurl">
<input type="hidden" name="allinfo" id="allinfo">
</form>
</div>
<div class="border-child-div" style="float: left;">
<div><p style="font-weight: bold;">Böcker</p>
<p><?php
$count = sql::get("SELECT COUNT(*) as c FROM lib_books")[0]["c"];
$count2 = sql::get("SELECT COUNT(DISTINCT isbn) as c FROM lib_books")[0]["c"];
$activateC = sql::get("SELECT COUNT(*) as c FROM lib_tusers WHERE active = 0")[0]["c"];
echo($count." böcker i biblioteket. ".$count2." unika");
?></p>
<a href="printbooks.php?a=all" target="_blank">Skriv ut streckkoder för alla böcker</a><br>
<a href="printbooks_single.php">Skriv ut streckkoder för enstaka böcker</a><br>
<a href="check_books.php">Checka av böcker</a><br>
<a href="sql/redir.php?do=books">Redigera böcker</a>
</div>
<div><p style="font-weight: bold;">Användare</p>
<a href="insertusers.php">Lägg till från Schoolsoft</a><br>
<a href="sql/redir.php?do=users">Redigera användare</a>
</div>
<div><p style="font-weight: bold;">Aktivera och återlämna</p>
<?php
if(intval($activateC) > 0) {
	echo <<<out
<a href="temp.php?a=activate">Aktivera användare</a><br>
out;
}
$toreturn = sql::get("SELECT COUNT(*) as c FROM lib_history WHERE returned IS NULL")[0]["c"];
if($toreturn != 0) {
?>
<a href="temp.php?a=return">Återlämna böcker</a>
<?php
}
?>
</div>
</div>
<?php
layout::footer();
?>