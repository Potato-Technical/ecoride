<?php

namespace App\Core;

use MongoDB\Client;
use MongoDB\Database as MongoDatabase;

class MongoConnection
{
    private static ?Client $client = null;
    private static ?MongoDatabase $database = null;

    /**
     * Retourne l'instance unique de la base MongoDB.
     */
    public static function getDatabase(): MongoDatabase
    {
        if (self::$database !== null) {
            return self::$database;
        }

        $config = require dirname(__DIR__, 2) . '/config/mongo.php';

        $dsn = sprintf(
            'mongodb://%s:%s',
            $config['host'],
            $config['port']
        );

        self::$client = new Client($dsn);
        self::$database = self::$client->selectDatabase($config['db']);

        return self::$database;
    }
}