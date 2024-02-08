<?php
class connector
{
	private static ?mysqli $bdd = null;

	public static function connect(): ?mysqli
	{
		if (self::$bdd == null || !self::$bdd->stat()) {
			$mysqli = new mysqli("localhost", "root", "", "base_pdi");
			if ($mysqli->connect_errno) {
				return null;
			}
			self::$bdd = $mysqli;
		}
		return self::$bdd;
	}
}
