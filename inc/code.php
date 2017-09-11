<?php
class code {
	static public function get($obj) {
		return strtoupper($obj["cat"].str_pad($obj["catindex"], 4, "0", STR_PAD_LEFT)."NR".str_pad($obj["bindex"], 3, "0", STR_PAD_LEFT));
	}	
	static public function find($code) {
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$res = $sql::get("SELECT * FROM lib_books WHERE cat = '".mb_strtolower(mb_substr($code, 0, 4))."' AND catindex = ".intval(mb_substr($code, 4, 4))." AND bindex = ".intval(mb_substr($code, 11, 3)));
		if($res) {			
			return $res[0];		
		} else {
			echo("cat = '".mb_strtolower(mb_substr($code, 0, 2))."' AND catindex = ".intval(mb_substr($code, 4, 4))." AND bindex = ".intval(mb_substr($code, 11, 3))."<br>");
			return false;		
		}	
	}
}
?>