<?php

namespace App\Models;

use App\Core\MongoConnection;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class ActivityLogRepository
{
    private Collection $collection;

    public function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/mongo.php';

        $this->collection = MongoConnection::getDatabase()
            ->selectCollection($config['activity_collection']);
    }

    /**
     * Enregistre un événement métier secondaire dans MongoDB.
     */
    public function insert(array $document): void
    {
        $payload = [
            'type' => $document['type'] ?? 'unknown',
            'user_id' => isset($document['user_id']) ? (int) $document['user_id'] : null,
            'created_at' => new UTCDateTime(),
            'meta' => $document['meta'] ?? [],
        ];

        $this->collection->insertOne($payload);
    }

    /**
     * Retourne les logs les plus récents.
     */
    public function findRecent(int $limit = 20): array
    {
        $cursor = $this->collection->find(
            [],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit,
            ]
        );

        return $cursor->toArray();
    }
}