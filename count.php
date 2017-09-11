<?php
require("inc/base.php");
layout::header();
echo("<a href=\"count.php\">Tecken</a> <a href=\"count.php?t=word\">Ord</a> <a href=\"count.php?t=row\">Rader</a>");
$rootpath = '.';
$fileinfos = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootpath)
);
function ok($var){
	$ret = true;
	if(substr($var, 0, 4) == ".git") { $ret = false; }
	if(substr($var, 0, 11) == "ext_modules") { $ret = false; }
	if(substr($var, -4) == ".ttf") { $ret = false; }
	if(substr($var, -4) == ".png") { $ret = false; }
	if(substr($var, -4) == ".jpg") { $ret = false; }
	if(substr($var, -4) == ".gif") { $ret = false; }
	if(substr($var, -4) == ".txt") { $ret = false; }
	if(substr($var, -4) == ".sql") { $ret = false; }
	if(substr($var, -4) == ".psd") { $ret = false; }
	if(substr($var, -7) == ".config") { $ret = false; }
	if(substr($var, 0, 12) == "projectfiles") { $ret = false; }
	if($var == ".htaccess") { $ret = false; }
	if($var == ".project") { $ret = false; }
	if($var == "colors.html") { $ret = false; }
	if($var == "counter.php") { $ret = false; }
	if($var == "README.md") { $ret = false; }
	if(strpos($var, ".") === false) { $ret = false; }
	return $ret;
}
function type($var) {
	return strtoupper(substr($var, strrpos($var, ".")+1));
}
$paths = [];
$tot = 0;
$typ = "tecken";
$typeCount = [];
if(isset($_GET["t"])) {
	if($_GET["t"] == "word") {
		$typ = "ord";
	} else
	if($_GET["t"] == "row") {
		$typ = "rader";
	}
}
foreach($fileinfos as $pathname => $fileinfo) {
    if (!$fileinfo->isFile()) continue;
	$path = substr($pathname, 2);
    if(ok($path)) {
		$file = file_get_contents($path);
		if(isset($_GET["t"])) {
			if($_GET["t"] == "word") {
				$size = str_word_count($file);
			} elseif($_GET["t"] == "row") {
				$size = substr_count($file, "\n");
			} else{
				$size = strlen($file);
			}
		} else{
			$size = strlen($file);
		}
		if(!isset($typeCount[type($path)])) {
			$typeCount[type($path)] = 0;
		}
		$typeCount[type($path)] = $typeCount[type($path)]+$size;
		$tot += $size;
		array_push($paths, ["file" => $path, "size" => $size]);
	}
}
$startDate = mktime(12, 0, 0, 2, 7, 2015);
$now = time();
$difference = $now-$startDate;
$speed = $difference/$tot;
$speedStr = "sekunder";
if($speed > 3600) {
	$speed = round($speed/60/60);
	$speedStr = "timmar";
} elseif($speed > 60) {
	$speed = round($speed/60);
	$speedStr = "minuter";
} else {
	$speed = round($speed, 2);
}
//echo("<p>".$speed." ".$speedStr." mellan varje ".$typ."</p>");
echo("<br />");
foreach($typeCount as $k => $v) {
	$typeCount[$k] = ($v/$tot)*100;
}
arsort($typeCount);
$col["PHP"] = "#0A0";
$col["JS"] = "#00F";
$col["CSS"] = "#F00";
foreach($typeCount as $k => $v) {
	if(isset($col[$k])) {
		$color = $col[$k];
	} else {
		$color = "rgb(".rand(0,255).",".rand(0,255).",".rand(0,255).")";
	}
	echo("<div style=\"min-width: 35px; display: inline-block; width: ".($v*9)."px; background: ".$color."; text-align: center;\"><p style=\"text-indent: 0px; color: #fff;\"><b>".$k."</b><br />".round($v)."%</p></div>");
}

usort($paths, function($a, $b) {
    return $b['size'] - $a['size'];
});
echo("<p style=\"font-weight: bold; margin: 0px; margin-top: 10px;\"><meter max=".$tot." value=".$tot." style=\"width: 600px; margin-right: 10px; margin-left: 100px;\"></meter>".$tot." ".$typ."</p>");
foreach($paths as $k => $v) {
	echo("<p style=\"margin: 0px; width: 110px; display: inline-block;\">".$paths[$k]["size"]." ".$typ.".</p><meter max=".$tot." value=".$paths[$k]["size"]." style=\"width: 600px; margin-right: 10px;\"></meter><p style=\"margin: 0px; display: inline;\">".$paths[$k]["file"]."</p><br />");
}
layout::footer();
?>