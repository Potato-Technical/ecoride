<?php

return [
    'host' => $_ENV['MONGO_HOST'] ?? getenv('MONGO_HOST') ?? 'mongo',
    'port' => $_ENV['MONGO_PORT'] ?? getenv('MONGO_PORT') ?? '27017',
    'db'   => $_ENV['MONGO_DB'] ?? getenv('MONGO_DB') ?? 'ecoride_nosql',
    'activity_collection' => $_ENV['MONGO_COLLECTION_ACTIVITY'] ?? getenv('MONGO_COLLECTION_ACTIVITY') ?? 'activity_logs',
];