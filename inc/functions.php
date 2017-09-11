<?php
function ssimp($txt) {
	$tabs = 5;
	$users = [];
	$txt = trim($txt);
	$txt = str_replace(['\n', '\t', '\r', '\r\n'], "!break!row!", $txt);
	$data = explode("!break!row!", $txt);
	foreach($data as $k => $v) {
		$data[$k] = trim($v);
	}
	$end = 4;
	array_splice($data, 0, array_search("Namn", $data));
	array_splice($data, count($data)-$end, count($data));
	$headers = array_splice($data, 0, $tabs);
	$tdata = $data;
	$c = 0;
	$id = 0;
	$data = [];
	foreach($tdata as $k => $v) {
		if(!isset($data[$id])) {
			$data[$id] = [];
		}
		$data[$id][$headers[$c]] = $v;
		if($c == $tabs-1) {
			$c = 0;
			$id++;
		} else {
			$c++;
		}
	}
	return $data;
}
function generateUserPassword() {
	$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
	$pList = [
		0,
		0,
		2,
		2,
		0,
		0,
		1,
		2,
		1,
		2,
		0,
		0
	];
	$find = true;
	$failsafe = 0;
	while($find) {
		$pass = "";
		for($c = 0; $c < 12; $c++) {
			$pass .= generateChar($pList[$c]);
		}
		$q = "SELECT COUNT(*) as c FROM lib_users WHERE pass = '".$pass."'";
		$ret = $sql::get($q)[0];
		if($ret["c"] === "0") {
			$find = false;
		}
		if($failsafe === 10000) {
			break;
		} else {
			$failsafe++;
		}
	}
	return $pass;
}
function generateChar($type) {
	$a = [
		"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"
	];
	$s = [
		"!", "@", "#", "$", "%", "&", "+", "?", "=", "-"
	];
	switch($type) {
		case 0:
			return $a[rand(0, count($a)-1)];
			break;
		case 1:
			return $s[rand(0, count($s)-1)];
			break;
		case 2:
			return rand(0, 9);
			break;
	}
}
function students2map($list) {
	echo("<progress max=".count($list)." id=\"perc\"></progress>
<div id=\"map\"></div>
<script>

function initMap() {
	var myLatLng = {lat: 56.0241393, lng: 14.1596971};

	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 8,
		center: myLatLng
	});
	var geocoder = new google.maps.Geocoder();
	var marker = [];
	var c = 0;
	var ad = [
");
$ok = false;
foreach($list as $k => $v) {
	if($ok == false) {
		echo("
	[
		\"".$v["Adress"]."\",
		\"".$v["Namn"]."\"
	]
");
		$ok = true;
	} else {
		echo(",
	[
		\"".$v["Adress"]."\",
		\"".$v["Namn"]."\"
	]
");
	}
}
echo("
];
	var q = 0;
	console.log(ad.length);
	var timer = setInterval(function() {
		var id = JSON.parse(JSON.stringify(q));
		geocoder.geocode(
			{'address': ad[id][0], componentRestrictions: {
				country: 'SE'
			}}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					marker[c] = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location,
						title: ad[q-1][1]
					});
					c++;
				} else {
					console.log(\"Hittade inte \"+ad[q][1]+\". FEL: \"+status);
				}
			}
		);
		if(q >= ad.length-1) {
			alert(\"Klart! \"+q+\" elever har ritats  ut.\");
			clearInterval(timer);
		}
		q++;
		document.getElementById(\"perc\").value = q;
	}, 1000);
}
</script>
<script async defer
src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyCO4gVXn-IQVqlqGGXO9VdXQJBGffQBrEM&callback=initMap&compponents=country:SE\">
</script>");
}
function students2heatmap($list) {
	echo("<progress max=".count($list)." id=\"perc\"></progress>
<div id=\"map\"></div>
<script>
function initMap() {
	var myLatLng = {lat: 56.0241393, lng: 14.1596971};
	var map = null;
	var geocoder = new google.maps.Geocoder();
	var marker = [];
	var c = 0;
	var ad = [
");
$ok = false;
foreach($list as $k => $v) {
	if($ok == false) {
		echo("
	[
		\"".$v["Adress"]."\",
		\"".$v["Namn"]."\"
	]
");
		$ok = true;
	} else {
		echo(",
	[
		\"".$v["Adress"]."\",
		\"".$v["Namn"]."\"
	]
");
	}
}
echo("
];
	var coords = [];
	var q = 0;
	var t = 0;
	var t2 = 0;
	var d = []
	var cont = true;
	var heatmap = null;
	var timer = setInterval(function() {
		if(t === 0) {
			var id = c;
			geocoder.geocode(
				{'address': ad[id][0], componentRestrictions: {
					country: 'SE'
				}}, function(results, status) {
					if (status === google.maps.GeocoderStatus.OK) {
						//console.log('found '+ad[id][1]);
						coords[c] = results[0].geometry.location;
						c++;
						q++;
						cont = true;
						t2 = 0;
					} else {
						//console.log(\"Hittade inte \"+ad[q][1]+\". FEL: \"+status);
						cont = false;
						t = t2;
						t2++;
					}
				}
			);
			if(q > ad.length-1) {
				clearInterval(timer);
				d = [];
				for(var w in coords) {
					d[w] = new google.maps.LatLng(parseInt(coords[w].lng), parseInt(coords[w].lat));
				}
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 8,
					center: myLatLng
				});
				heatmap = new google.maps.visualization.HeatmapLayer({
					data: d,
					map: map
				});
				var gradient = [
					'rgba(0, 255, 255, 0)',
					'rgba(0, 255, 255, 1)',
					'rgba(0, 191, 255, 1)',
					'rgba(0, 127, 255, 1)',
					'rgba(0, 63, 255, 1)',
					'rgba(0, 0, 255, 1)',
					'rgba(0, 0, 223, 1)',
					'rgba(0, 0, 191, 1)',
					'rgba(0, 0, 159, 1)',
					'rgba(0, 0, 127, 1)',
					'rgba(63, 0, 91, 1)',
					'rgba(127, 0, 63, 1)',
					'rgba(191, 0, 31, 1)',
					'rgba(255, 0, 0, 1)'
				]
				heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
				heatmap.set('radius', heatmap.get('radius') ? null : 200);
				heatmap.set('opacity', heatmap.get('opacity') ? null : 0.5);
				heatmap.setMap(heatmap.getMap() ? null : map);
			}
			if(cont == true) {
				q++;
			}
			document.getElementById(\"perc\").value = q;
		} else {
			t--;
		}
	}, 50);
}
</script>
<script async defer
src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyCO4gVXn-IQVqlqGGXO9VdXQJBGffQBrEM&callback=initMap&compponents=country:SE&libraries=visualization\">
</script>");
}
?>