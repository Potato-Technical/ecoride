<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

use App\Config\Database;

$db = Database::pdo();
echo "Connexion OK → " . $db->query("SELECT 1")->fetchColumn();
