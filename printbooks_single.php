<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/books.php");
require("inc/cats.php");
layout::header();
?>
<a href="#" onclick="print();">Skriv ut</a>
<input type="text" placeholder="Söksträng" oninput="bookSearch(this);">
<script>
var enabledBooks = [];
var results = [];
function bookSearch(obj) {
	var q = obj.value;
	results = [];
	for(var c in book) {
		if(book[c].title.toLowerCase().includes(q.toLowerCase())) {
			results.push(book[c]);
		} else if(book[c].subtitle.toLowerCase().includes(q.toLowerCase())) {
			results.push(book[c]);
		}
	}
	var o = "";
	for(var c in results) {
		o += "<div onclick=\"enabledBooks.push(results["+c+"]);printEnabledBooks();\">"+results[c].code+": "+results[c].title+"</div>";
	}
	document.getElementById("res").innerHTML = o;
}
function printEnabledBooks() {
	var out = "";
	for(var c in enabledBooks) {
		out += bookify(enabledBooks[c]);
	}
	document.getElementById("enabledBooks").innerHTML = out;
}
function bookify(book) {
	return "<div style=\"text-align: center; float: left; border: 1px solid #000; margin: 10px; padding: 0px 5px; max-width: 6cm;\"><p style=\"line-height: 0pt;\">"+book.title+"</p><p style=\"line-height: 0pt; font-style: italic;\">"+book.subtitle+"</p><img src=\"barcode.php?text="+book.code+"&size=40\"><p style=\"line-height: 0pt;\">"+book.code+"</p></div>";
}
<?php
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$cats = $sql::get("SELECT * FROM lib_cats");
$res = $sql::get("SELECT * FROM lib_books ORDER BY cat DESC");
$books = [];
echo("var book = [");
foreach($res as $v) {
	array_push($books, "{
	title: '".addslashes($v["title"])."',
	subtitle: '".addslashes($v["subtitle"])."',
	code: '".code::get($v)."',
	cat: '".mb_substr($v["cat"], 0, 2, "UTF-8")."',
	subcat: '".mb_substr($v["cat"], 2, 2, "UTF-8")."'
}");
}
echo(implode(",", $books));
?>];
</script>
<table><tbody><tr>
<td><p id="res">Resultat</p></td>
<td style="vertical-align: top;"><p id="enabledBooks">böcker</p></td>
</tr></tbody></table>
</body>
</html>