<?php

namespace System\Database\DBConnection;

use PDO;
use PDOException;


class DBConnection
{
    private static $dbConnectionInstance = null;

    private function __construct(){

    }

    public static function getDbConnectionInstance(){
        if(self::$dbConnectionInstance === null){
            $DBConnectionInstance = new DBConnection();
            self::$dbConnectionInstance = $DBConnectionInstance->dbConnection();
        }
        return self::$dbConnectionInstance;
    }

    private function dbconnection(){

        $options =[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];

        try {
           return  new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSERNAME, DBPASSWORD,$options);

            echo "Connected successfully";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return false;
        }
    }

    public static function newInsertId(){
        return self::getDbConnectionInstance()->lastInsertId();

    }

}












