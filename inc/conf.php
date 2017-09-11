<?php
class conf {
	public static function get() {
		return [
			"config" => [
				"domain" => "localhost",
				"folder" => "/lib",
				"borrowTime" => [	// I dagar
					0 => "14",		// Elev
					1 => "30",		// Lärare
					2 => "365"		// Admin
				],
				"scanUrl" => "/index.php"
			],/*
			"sql" => [
				"server" => "localhost",
				"user" => "root",
				"pass" => "",
				"db" => "lib"
			],*/
			"sql" => [
				"server" => "localhost",
				"user" => "root",
				"pass" => "",
				"db" => "lib"
			]
		];
	}
}
?>