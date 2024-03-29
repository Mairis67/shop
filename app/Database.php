<?php

namespace App;

use Doctrine\DBAL\DriverManager;

class Database
{
    private static $connection = null;

    public static function connection()
    {
        if(self::$connection === null) {
            $connectionParams = [
                'dbname' => 'shop',
                'user' => 'mairis',
                'password' => '!',
                'host' => 'localhost',
                'driver' => 'pdo_mysql'
            ];
            self::$connection = DriverManager::getConnection($connectionParams);
        }
        return self::$connection;
    }
}