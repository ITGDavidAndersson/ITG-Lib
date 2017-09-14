<?php
session_name("ITGlib");
session_start();
require("inc/base.php");
require("inc/ad.php");
if(isset($_GET["a"])) {
	if($_GET["a"] === "logout") {
		unset($_SESSION["tuser"]);
		session_destroy();
		header("Location: index.php?msg=".urlencode("Du har loggats ut"));
	} elseif($_GET["a"] === "checkout") {
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$allok = true;
		$books = explode("!_BREAK_!", $_POST["books"]);
		foreach($books as $v) {
			
			$ok = $sql->set("SELECT ;");
			
			
			
			$ok = $sql->set("INSERT INTO lib_history(book, user) VALUES(\"".$v."\", ".$_SESSION["tuser"][0]["id"].");");
			if(!$ok) {
				if($allok === true) {
					$allok = [];
				}
				array_push($allok, $v);
			}
		}
		if($allok !== true) {
			header("Location: index.php?msg=".urlencode("Alla böcker gick inte att låna ut. Försök gärna igen med följande böcker: ".implode(", ", $allok)."."));
		} else {
			header("Location: index.php?msg=".urlencode("Böckerna har blivit utlånade till ".$_SESSION["tuser"][0]["name"]."."));
		}
	}
} elseif(isset($_POST["user"])) {
	if(!isset($_POST["pnamn"])) {
		$username = $_POST["user"];
		$password = $_POST["pass"];
		$ad = new ad($username, $password);
		if($ad->status !== "connected") {
			header("Location: index.php?msg=".urlencode("Du har <b>inte</b> loggats in. "));
		} else {
			$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
			$ok = $sql->get("SELECT * FROM lib_adusers WHERE username = \"".strtolower($_POST["user"])."\" AND active = 1;");
			if(count($ok) > 0) {
				$ok = $sql->get("SELECT * FROM lib_users WHERE id = \"".$ok[0]["con"]."\";");
				if(count($ok) > 0) {
					$_SESSION["tuser"] = [$ok[0], time()];
					header("Location: index.php?msg=".urlencode("Du har loggats in"));
				}
			} else {
				$ok = $sql->get("INSERT INTO lib_adusers(username, active) VALUES(\"".strtolower($username)."\", 1);");
				if(count($ok) > 0) {
					$_SESSION["tuser"] = [["username" => strtolower($username)], time()];
					header("Location: index.php?msg=".urlencode("Ditt konto har registrerats."));
				} else {
					header("Location: index.php?msg=".urlencode("Ditt konto har inte aktiverats."));
				}
			}
		}
		/*
		if(count($ok) > 0) {
			$_SESSION["tuser"] = [$ok[0], time()];
			header("Location: index.php?msg=".urlencode("Du har loggats in"));
		} else {
			header("Location: index.php?msg=".urlencode("Du har <b>inte</b> loggats in. "));
		}*/
	}
}
layout::header();
if(!isset($_SESSION["books"])) {
	$_SESSION["books"] = [];
}
echo <<<OUT
<script>
function msg(msg) {
	document.getElementById("msg").style.display = "block";
	setTimeout(function() {
		document.getElementById("msg").classList.remove("disabled");
	}, 1);
	document.getElementById("txt").children[0].innerHTML = msg;
}
function exitMsg() {
	document.getElementById("msg").classList.add("disabled");
	setTimeout(function() {
		document.getElementById("msg").style.display = "none";
	}, 205);
}
</script>
<div id="msg" class="disabled">
<div id="container"><div onclick="exitMsg();" style="text-align: right;">X</div><div id="txt"><p>Msg</p></div></div>
</div>
OUT;
$config = conf::get();
if(isset($_SESSION["user"])) {
	echo <<<OUT
<script>
function enableScanner(o) {
	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
	if(isAndroid) {
		location.href = "http://zxing.appspot.com/scan?ret=http%3A%2F%2F
OUT;
	echo $config["config"]["domain"];
	echo <<<OUT
%2Fadd.php%3Fc%3D%7BCODE%7D&SCAN_FORMATS=CODE_128";
	}
	o.placeholder = "Skanna en streckkod";
}
function disableScanner(o) {
	o.placeholder = "Klicka här för att låna/lämna";
}
function checkCode(o) {
	if(o.value.length === 13) {
		scan(o.value);
		console.log(o.value);
		o.value = "";
	}
}
function scan(code) {
	b_ajax_get(scanMsg, "add.php?t=scan&c="+code);
	document.getElementById("books").innerHTML = "Letar...";
	document.getElementById("bookAddList").innerHTML = "Uppdaterar...";
}
function scanMsg(msg) {
	document.getElementById("books").innerHTML = msg;
	b_ajax_get(updBooklist, "bookaddlist.php");
}
function updBooklist(txt) {
	document.getElementById("bookAddList").innerHTML = txt;
}
</script>
<input type="text" id="scanner" name="code" placeholder="Klicka här för att låna/lämna" onfocus="enableScanner(this);" onblur="disableScanner(this);" oninput="checkCode(this);" size=16>
<p id="books">
OUT;
echo $_GET["msg"];
echo <<<OUT
</p>
OUT;
} else {
	echo <<<OUT
<script>
function enableScanner(o) {
	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
	if(isAndroid) {
		link = "http://zxing.appspot.com/scan?ret=http%3A%2F%2F
OUT;
	echo $config["config"]["domain"];
	echo <<<OUT
%2Flogin.php%3Ft%3Dscan%26c%3D%7BCODE%7D&SCAN_FORMATS=CODE_128";
		location.href = link;
	}
	o.placeholder = "Skanna ditt lånekort";
}
function disableScanner(o) {
	if(o.value !== "") {
		o.value = "";
	}
	o.placeholder = "Klicka här, skanna sen ditt lånekort";
}
function checkCode(o) {
	if(o.value !== "") {
		if(o.value.length === 6) {
			msg("<p>Personlig kod</p><form action='login.php?id="+o.value+"' method='POST'><input type='password' name='pass' id='codeEntry' size=16><input type='hidden' name='id' value='"+o.value+"'><br><input type='submit' value='Logga in'></form>");
			o.blur();
			document.getElementById("codeEntry").focus();
			//location.href = "login.php?id="+o.value;
		} else {
			o.placeholder = "Fel kod. Försök igen";
			//o.value = "";
		}
	}
}
</script>
<!--<input type="text" id="scanner" name="code" placeholder="Klicka här, skanna sen ditt lånekort" onfocus="enableScanner(this);" onblur="disableScanner(this);" oninput="checkCode(this);" size=16>-->
OUT;
}
?>
<style>
input {
	padding: 5px;
	transition: border-color 500ms;
}
input[disabled] {
	border-color: #f88;
}
.topright {
	position: fixed;
	top: 10pt;
	right: 10pt;
}
.topright > p {
	margin: 2pt;
	background-color: #fff;
	border: 1px solid #aaa;
	border-radius: 5pt;
	padding: 5pt;
}
.topright > p:empty {
	display: none;
}
#logoutCountdown {
	transition: background-color 100ms;
}
#bookinfo.off {
	opacity: 0;
}
#bookinfo {
	opacity: 1;
	transition: opacity 500ms;
}
#bookinfotext {
	border: 1px solid #aaa;
	border-radius: 5pt;
	padding: 5pt;
}
.borrowButton {
	display: inline-block;
	margin: 2pt;
	padding: 3pt;
	border: 1px solid #000;
	border-radius: 5pt;
}
.helpbut {
	border: 1px solid #aaa;
	padding: 2pt;
	background-color: #fff;
}
.helpbut:hover {
	background-color: #ddd;
}
.bblist {
	overflow: hidden;
	max-height: 18pt;
	border: 1px solid #ddd;
	transition: max-height 500ms;
}
.bblist:hover {
	max-height: 100pt;
}
table * {
	text-align: left;
}
</style>
<?php
if(isset($_SESSION["tuser"])) {
	$realname = $_SESSION["tuser"][0]["name"];
	echo <<<out
<p><a href="index.php?a=logout">Logga ut {$realname}</a></p>
out;
?>
<script>
var logouttime = 120;
var logoutTimer = setTimeout(logout, logouttime*1000);
var updLogoutTextTimer = setInterval(updLogoutCountdown, 1000);
function updLogoutTimer() {
	clearTimeout(logoutTimer);
	logoutTimer = setTimeout(logout, logouttime*1000);
	logouttime = 120;
	document.getElementById("logoutCountdown").style.display = "none";
}
function updLogoutCountdown() {
	if(logouttime <= 30) {
		document.getElementById("logoutCountdown").style.display = "block";
		document.getElementById("logoutCountdown").innerHTML = "Du loggas ut om "+logouttime+"s";
	}
	if(logouttime <= 10) {
		document.getElementById("logoutCountdown").style.backgroundColor = "#faa";
		setTimeout(function() {
			document.getElementById("logoutCountdown").style.backgroundColor = "#fff";
		}, 100);
	}
	logouttime--;
}
function logout() {
	location.href = "./?a=logout";
}
document.addEventListener("mousemove", updLogoutTimer);
document.addEventListener("keydown", updLogoutTimer);
var books = [];
<?php
$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
$books = $sql->get("SELECT * FROM lib_books GROUP BY title");
foreach($books as $v) {
	echo("books.push({title: \"".$v["title"]."\", info: ".JSON_encode($v["info"]).", code: \"".strtoupper($v["cat"].str_pad($v["catindex"], 4, "0", STR_PAD_LEFT)."NR".str_pad($v["bindex"], 3, "0", STR_PAD_LEFT))."\", isbn: \"".$v["isbn"]."\"});
");
}
?>
function checkBook(q) {
	var found = false;
	for(var c in books) {
		if(books[c].title == q.value) {
			document.getElementById("bookinfo").classList.remove("off");
			document.getElementById("booklink").innerHTML = "<a href=\"book.php?isbn="+books[c].isbn+"\">Mer info</a>";
			document.getElementById("bookinfotext").innerHTML = "<i>"+books[c].info+"</i>";
			found = true;
		}
	}
	if(found === false) {
		document.getElementById("booklink").innerHTML = "";
		document.getElementById("bookinfo").classList.add("off");
	}
}
function help() {
	alert("Låna böcker antingen genom att skanna koden på boken, eller genom att skriva bokens namn. Ifall du får fel namn när du skannar, eller boken inte finns, så skriver du in bokens titel och klickar på låna ändå.\nAlla böcker har 14 dagars lånetid. Är inte boken återlämnad då så kommer skolan vidta åtgärder vilka kan resultera i att du blir betalningsskyldig.");
}
var borrowList = [];
function borrow() {
	var cbook = document.getElementById("cbook").value;
	var found = false;
	for(var c in books) {
		if(books[c].title == cbook) {
			found = books[c];
		}
	}
	var ok = true;
	if(found === false) {
		ok = confirm("Är du säker på att du har skrivit in rätt titel på boken?");
		found = {title: cbook, info: "", code: "", isbn: ""};
	}
	if(ok === true) {
		borrowList.push(found);
		updBorrowList();
		document.getElementById("cbook").value = "";
	}
}
function removeBook(id) {
	borrowList.splice(id, 1);
	updBorrowList();
}
function updBorrowList() {
	var out = "<table>";
	var booksList = "";
	for(var c in borrowList) {
		out += "<tr><td>"+borrowList[c].title+"</td><td><span onclick=\"removeBook("+c+");\">x</span></td></tr>";
		if(booksList !== "") {
			booksList += "!_BREAK_!";
		}
		booksList += borrowList[c].title;
	}
	out += "</table>";
	document.getElementById("borrowBooks").innerHTML = out+"<form action=\"index.php?a=checkout\" method=\"POST\"><input type=\"hidden\" name=\"books\" value=\""+booksList+"\"><input type=\"submit\" value=\"Slutför lån av böcker\"></form>";
}
</script>
<div class="topright"><p id="logoutCountdown"></p><p id="borrowBooks">Böcker</p></div>
<h2>Låna</h2>
<form action="index.php?a=borrow" method="POST">
<input name="book" id="cbook" list="books" placeholder="Klicka och skanna bok" onfocus="this.style.border = '5px solid #0f0';" onblur="this.style.border = null;" oninput="checkBook(this);">
<datalist id="books">
<?php
$books = $sql->get("SELECT * FROM lib_books GROUP BY title");
foreach($books as $v) {
	echo("<option value=\"".$v["title"]."\">");
}
?>
</datalist>
<span id="booklink"></span><br>
<p onclick="borrow();" class="borrowButton">Låna</p> <span onclick="help();" class="helpbut">?</span> <i>14 dagars lånetid för alla böcker.</i>
<div id="bookinfo" class="off">
<p id="bookinfotext"></p>
</div>
</form>
<?php
	if(isset($_SESSION["tuser"][0]["id"])) {
		$borrowedBooks = $sql->get("SELECT * FROM lib_history WHERE user = ".$_SESSION["tuser"][0]["id"]." AND returned IS NULL;");
		echo("<div class=\"bblist\"><h3>Ej återlämnade böcker (".count($borrowedBooks).")</h3><table><tbody>");
		foreach($borrowedBooks as $v) {
			$tid = ceil((((intval(strtotime($v["time"]))+(60*60*24*14))-time()))/(60*60*24));
			if(intval($tid) < 0) {
				$tid = "<span style=\"color: #f00;\">".$tid."</span>";
			} elseif(intval($tid) < 4) {
				$tid = "<span style=\"color: #f80;\">".$tid."</span>";
			}
			echo("<tr><th><p>".$v["book"]."</p></th><td><p>".$tid." dagar kvar</p></td></tr>");
		}
		echo("</tbody></table></div>");
	}
}/* elseif(isset($_GET["a"])) {
?>
<h2>Registrera</h2>
<script>
var okmsg = true;
function checkreg(q) {
	document.getElementById("submitbut").disabled = true;
	var val = [];
	val[0] = document.getElementById("namn").value;
	val[1] = document.getElementById("user").value;
	val[2] = document.getElementById("pass").value;
	val[3] = document.getElementById("pass2").value;
	okmsg = true;
	for(var c = 0; c <= 3; c++) {
		if(val[c] === "") {
			okmsg = "Du måste fylla i alla rutorna";
		} else if(val[c].length < 6) {
			okmsg = "Alla rutorna måste innehålla MINST 6 tecken";
		}
	}
	if(typeof q !== "undefined") {
		if(val[2] !== val[3]) {
			okmsg = "Du måste skriva samma lösenord i båda rutorna";
		}
		if(okmsg !== true) {
			alert(okmsg);
			event.preventDefault();
		return false;
		} else {
			return true;
		}
	} else {
		if(okmsg === true) {
			document.getElementById("submitbut").disabled = false;
		}
	}
}
function vispass(q) {
	if(q.checked === true) {
		document.getElementById("pass").type = "text";
		document.getElementById("pass2").type = "text";
	} else {
		document.getElementById("pass").type = "password";
		document.getElementById("pass2").type = "password";
	}
}
</script>
<form action="index.php" method="POST" onsubmit="checkreg(this);">
<input type="text" id="namn" name="pnamn" placeholder="För och efternamn" oninput="checkreg();" autocomplete="off"><br>
<input type="text" id="user" name="user" placeholder="Användarnamn" oninput="checkreg();" autocomplete="off"><br>
<input type="password" id="pass" name="pass" placeholder="Lösenord" oninput="checkreg();"> <input type="checkbox" onclick="vispass(this);">Visa<br>
<input type="password" id="pass2" name="pass2" placeholder="Lösenord igen" oninput="checkreg();"><br>
<input type="submit" value="Registrera" id="submitbut" disabled>
</form>
<?php
} elseif(isset($_POST["pnamn"])) {
	$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
	$ok = $sql->set("INSERT INTO lib_tusers(namn, user, pass) VALUES(\"".$_POST["pnamn"]."\", \"".$_POST["user"]."\", \"".crypt($_POST["pass"], "lib")."\");");
	if($ok !== false) {
		echo("Ditt konto har skapats och kommer så snart som möjligt att aktiveras av David ifall dina uppgifter blir godkända. Godkända uppgifter är: <i>Användarnamn som inte innehåller obscena ord, ett lösenord som är någorlunda säkert (något mer än 12341234), och ditt riktiga namn. <a href=\"index.php\">Tillbaka till startsidan</a>");
	} else {
		echo("<br>Ditt konto kunde inte skapas. <a href=\"index.php?a=reg\">Försök igen</a>");
	}
}*/ elseif((!isset($_POST["namn"])) && (!isset($_POST["pnamn"]))) {
?>
<h1>Logga in</h1>
<form action="index.php" method="POST">
<input type="text" name="user" id="loginuser" placeholder="Användarnamn" autocomplete="off"><br>
<input type="password" name="pass" placeholder="Lösenord" autocomplete="off"><br>
<input type="submit" value="Logga in">
</form>

<?php
	// <a href="index.php?a=reg">Registrera konto</a>
}
//unset($_SESSION["user"]);
layout::footer();
?>