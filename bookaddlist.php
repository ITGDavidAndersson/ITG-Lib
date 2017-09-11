<?php
if(!class_exists("sql")) {
	require("inc/base.php");
}
if(!class_exists("books")) {
	require("inc/books.php");
}
?>
<h3>Att låna</h3><p>
<?php
if(count($_SESSION["books"]) === 0) {
	echo "<p>Inga böcker skannade";
} else {
	echo "<p>";
	foreach($_SESSION["books"] as $k => $v) {
		$book = new books(code::find($v)["isbn"]);
		echo $book->get()["title"]."<br>";
	}
	echo <<<OUT
</p><a href="signout.php">Låna</a>
OUT;
}
?>
</p>