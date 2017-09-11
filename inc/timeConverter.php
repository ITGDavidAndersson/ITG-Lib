<?php
class timeConverter {
	public static function timeSince($ts, $t) {
		$ts = $ts;
		switch($t) {
			case "short":
				$type = 0;
				break;
			case "long":
				$type = 1;
				break;
		}
		$str = [
			"s" => ["sek", " sekunder"],
			"m" => ["min", " minuter"],
			"t" => ["tim", " timmar"],
			"d" => ["d", " dagar"]
		];
		$units = [
			[0, 1, "s"],
			[60, 1, "s"],
			[60*60, 60, "m"],
			[60*60*24, (60*60), "t"]
		];
		$ret = false;
		foreach($units as $k => $v) {
			if($ts < $units[$k][0]) {;
				$ret = true;
				return floor($ts/$v[1]).$str[$v[2]][$type];
				break;
			}
		}
		if($ret === false) {
			return ceil($ts/(60*60*24)).$str["d"][$type];
		}
		return $ts;
	}
}
?>