<?php
class books {
	static private $book;
	public function __construct($id = null) {
		$sql = new sql(conf::get()["sql"]["server"], conf::get()["sql"]["user"], conf::get()["sql"]["pass"], conf::get()["sql"]["db"]);
		$vals = "";
		$res = $sql::get("SELECT * FROM lib_books WHERE isbn = '".$id."'");
		if($res) {
			self::$book = $res[0];
		}
	}
	public function get(){
		return self::$book;
	}
}
?>