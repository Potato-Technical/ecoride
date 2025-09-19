<?php
namespace App\Core;

// Inclusion de la fonction PDO
require_once ROOT . 'Config/Database.php';

abstract class Model {
    protected \PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(); // Appel à la fonction de connexion
    }
}
