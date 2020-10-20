<?php

class Database {

    private static $db;

    public static function createDB() {
        if (!self::$db) {
            $serverName = DB_HOST;
            $username = DB_USER;
            $password = DB_PASS;
            $database = DB_NAME;

            self::$db = new PDO("mysql:host=$serverName;dbname=$database", $username, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function getDB() {
        self::createDB();
        return self::$db;
    }

    public static function freeDB() {
        if (self::$db) {
            self::$db = null;
        }
    }

}

?>
