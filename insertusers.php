<?php
session_name("ITGlib");
session_start();
if(!isset($_SESSION["admin"])) {
	header("Location: index.php");
}
require("inc/base.php");
if(isset($_POST["import"])) {
	if($_POST["type"] == "0") {
		$data = ssimp($_POST["import"]);
		$replaces = [
			"Namn" => "name",
			"Adress" => "address",
			"Mobiltelefon" => "mphone",
			"Telefon bostad" => "hphone",
			"E-post" => "mail"
		];
	} else {
		$data = ssimp($_POST["import"]);
		$replaces = [
			"Namn" => "name",
			"Adress" => "address",
			"Mobiltelefon" => "mphone",
			"Telefon arbete" => "hphone",
			"E-post" => "mail"
		];
	}
	$tdata = $data;
	$data = [];
	foreach($tdata as $k => $v) {
		$data[$k] = [];
		foreach($v as $k2 => $v2) {
			$id = str_replace(array_keys($replaces), $replaces, $k2);
			$data[$k][$id] = "\"".trim(stripslashes($v2))."\"";
		}
	}
	foreach($data as $k => $v) {
		$data[$k]["pass"] = "temp";
	}
	$keys = array_keys($data[0]);
	array_push($keys, "type");
	array_push($keys, "uid");
	$ret = "<table><tbody>";
	$ok = true;
	foreach($data as $v) {
		$pass = generateUserPassword();
		$v["pass"] = "\"".sha1($pass)."\"";
		$v["type"] = $_POST["type"];
		$name = $v["name"];
		$names = explode(" ", $v["name"]);
		foreach($names as $k2 => $v2) {
			$names[$k2] = str_replace("\"", "", trim($v2));
		}
		if(count($names) !== 1) {
			$v["uid"] = mb_substr($names[count($names)-1], 0, 2, "UTF-8").mb_substr($names[0], 0, 2, "UTF-8").generateChar(2).generateChar(2).generateChar(1);
		} else {
			$v["uid"] = mb_substr($names[0], 0, 4, "UTF-8").generateChar(2).generateChar(2).generateChar(1);
		}
		$v["uid"] = mb_strtolower($v["uid"], "UTF-8");
		$user = $v["uid"];
		$v["uid"] = "'".$v["uid"]."'";
		$inp = "(".implode(",", $v).")";
		$q = "INSERT INTO lib_users(".implode(", ", $keys).") VALUES ".$inp;
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$stat = $sql::set($q);
		$stat = true;
		if($stat == true) {
			$ret .= "<tr><td>".str_replace("\"", "", $name)."</td><td>".$user."</td><td><b>".$pass."</b></td></tr>";
		} else {
			$ok = false;
			echo("FEL");
			break;
		}
	}
	if($ok == true) {
		echo($ret."</tbody></table>");
	}
} else {
	layout::header();
?>
<script>
function importMsg(stat) {
	if(stat == "FEL") {
		alert("Importering misslyckades");
	} else {
		alert("Importering lyckades. Skriv upp lösenorden direkt. De går INTE att återhämta senare.");
	}
	el("output").innerHTML = stat;
}
</script>
<p>Klistra in koden från Schoolsoft här</p>
<textarea id="import" placeholder="Kod här. Namn, personnr, adress, mobiltelefonnr, telefon-bostad, och epost" style="min-width: 50%; min-height: 20%;">
</textarea><br>
Elev<input type="radio" name="usertype" onclick="el('studBut').style.display = 'block';el('teachBut').style.display = 'none';"><br>
Lärare<input type="radio" name="usertype" onclick="el('studBut').style.display = 'none';el('teachBut').style.display = 'block';">
<br>
<button onclick="b_ajax_get(importMsg, 'insertusers.php', 'POST', 'import='+JSON.stringify(el('import').value)+'&type=0');" id="studBut" style="display: none;">Lägg till elever</button>
<button onclick="b_ajax_get(importMsg, 'insertusers.php', 'POST', 'import='+JSON.stringify(el('import').value)+'&type=1');" id="teachBut" style="display: none;">Lägg till Lärare</button><br>
<pre id="output"></pre>
<?php
	layout::footer();
}
?>