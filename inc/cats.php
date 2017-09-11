<?php
class cats {
	static private $cats;
	public static function init() {
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$vals = "";
		$res = $sql::get("SELECT * FROM lib_cats");
		if($res !== false) {
			$cats = [];
			$c = 50;
			while(count($res) !== 0) {
				foreach($res as $k => $v) {
					if($v["parent"] === NULL) {
						$cats[$v["name"]]["name"] = ucfirst($v["fullname"]);
						unset($res[$k]);
					} else {
						if(isset($cats[$v["parent"]])) {
							$cats[$v["parent"]][$v["name"]] = ucfirst($v["fullname"]);
							unset($res[$k]);
						}
					}
				}
				$c--;
				if($c === 0) {
					$res = [];
				}
			}
			self::$cats = $cats;
		}
	}
	public static function get($code = NULL){
		if($code === NULL) {
			return false;
		} else {
			$p1 = strtolower(substr($code, 0, 2));
			$p2 = strtolower(substr($code, 2, 2));
			return self::$cats[$p1]["name"]." > ".self::$cats[$p1][$p2];
		}
	}
}
cats::init();
?>